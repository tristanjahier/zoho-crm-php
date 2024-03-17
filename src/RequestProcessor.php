<?php

declare(strict_types=1);

namespace Zoho\Crm;

use Exception;
use Http\Promise\Promise as HttpPromiseInterface;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\ErrorHandlerInterface;
use Zoho\Crm\Contracts\HttpLayerInterface;
use Zoho\Crm\Contracts\PaginatedRequestInterface;
use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Contracts\ResponseParserInterface;
use Zoho\Crm\Exceptions\AsyncBatchRequestException;
use Zoho\Crm\Exceptions\PaginatedRequestInBatchExecutionException;

/**
 * The API request processor.
 */
class RequestProcessor
{
    /** The client to which this processor is attached */
    protected ClientInterface $client;

    /** The HTTP layer */
    protected HttpLayerInterface $httpLayer;

    /** The response parser */
    protected ResponseParserInterface $responseParser;

    /** The error handler */
    protected ErrorHandlerInterface $errorHandler;

    /**
     * The callbacks to execute before each request.
     *
     * @var callable[]
     */
    protected array $preExecutionHooks = [];

    /**
     * The callbacks to execute after each request.
     *
     * @var callable[]
     */
    protected array $postExecutionHooks = [];

    /**
     * The middlewares to apply to each request before execution.
     *
     * @var callable[]
     */
    protected array $middlewares = [];

    /**
     * The constructor.
     *
     * @param Contracts\ClientInterface $client The client to which it is attached
     * @param Contracts\HttpLayerInterface $httpLayer The HTTP layer
     * @param Contracts\ResponseParserInterface $responseParser The response parser
     * @param Contracts\ErrorHandlerInterface $errorHandler The error handler
     */
    public function __construct(
        ClientInterface $client,
        HttpLayerInterface $httpLayer,
        ResponseParserInterface $responseParser,
        ErrorHandlerInterface $errorHandler
    ) {
        $this->client = $client;
        $this->httpLayer = $httpLayer;
        $this->responseParser = $responseParser;
        $this->errorHandler = $errorHandler;

        $this->passClientPreferencesToComponents();
    }

    /**
     * Pass the client preferences to the components that need them.
     */
    protected function passClientPreferencesToComponents(): void
    {
        foreach ([
            $this->httpLayer,
            $this->responseParser,
            $this->errorHandler,
        ] as $component) {
            if ($component instanceof ClientPreferencesAware) {
                $component->setClientPreferences($this->client->preferences());
            }
        }
    }

    /**
     * Execute a request and get a formal and generic response object.
     *
     * @param Contracts\RequestInterface $request The request to execute
     * @return Response
     */
    public function executeRequest(RequestInterface $request): ResponseInterface
    {
        if ($request instanceof PaginatedRequestInterface && $request->mustBePaginatedAutomatically()) {
            return $this->executePaginatedRequest($request);
        }

        $response = $this->sendRequest($request);

        return $this->responseParser->parse($response, $request);
    }

    /**
     * Process a request and send it, synchronously or asynchronously.
     *
     * If synchronous, the returned value is the response of the API.
     * If asynchronous, the returned value is a promise that needs to be settled afterwards.
     *
     * @param Contracts\RequestInterface $request The request to process
     * @param bool $async (optional) Whether the resulting request must be asynchronous or not
     */
    protected function sendRequest(
        RequestInterface $request,
        bool $async = false
    ): HttpResponseInterface|HttpPromiseInterface {
        // Use a copy of the request, so that all modifications potentially
        // brought by middleware are not affecting the original request.
        $request = $request->copy();

        $this->applyMiddlewaresToRequest($request);

        // Generate a "unique" ID for the request execution
        $execId = $this->generateRandomId();

        // Create a PSR-7 HTTP request, from the API request object.
        $httpRequest = $this->httpLayer->createRequest(
            $request->getHttpMethod(),
            $this->client->getEndpoint() . $request->getUrlPath() . '?' . $request->getUrlParameters(),
            $request->getHeaders(),
            (string) $request->getBody()
        );

        $this->firePreExecutionHooks($request->copy(), $execId);

        if ($async) {
            return $this->httpLayer->sendAsyncRequest(
                $httpRequest,
                function ($response) use ($request, $execId) {
                    $this->firePostExecutionHooks($request->copy(), $execId);

                    return $response;
                }
            );
        }

        try {
            $response = $this->httpLayer->sendRequest($httpRequest);
        } catch (Exception $e) {
            $this->handleException($e, $request);
        }

        $this->firePostExecutionHooks($request->copy(), $execId);

        return $response;
    }

    /**
     * Generate a random alpha-numeric string of 16 characters.
     */
    protected function generateRandomId(): string
    {
        return bin2hex(random_bytes(8));
    }

    /**
     * Execute a paginated request.
     *
     * @param Contracts\PaginatedRequestInterface $request The request to execute
     * @return Response
     */
    protected function executePaginatedRequest(PaginatedRequestInterface $request): ResponseInterface
    {
        $paginator = $request->getPaginator();
        $concurrency = $request->mustBePaginatedConcurrently() ? $request->getConcurrency() : 1;
        $pageResponses = [];

        // Fetch pages until there is no more data to fetch.
        do {
            $batchRequests = [];

            for ($i = 0; $i < $concurrency; $i++) {
                $batchRequests[] = $paginator->getNextPageRequest();
            }

            if ($concurrency > 1) {
                $batchResponses = $this->executeAsyncRequestBatch($batchRequests);
            } else {
                $batchResponses = [$this->executeRequest($batchRequests[0])];
            }

            foreach ($batchResponses as $pageResponse) {
                $pageResponses[] = $pageResponse;
                $paginator->handlePage($pageResponse);
            }
        } while ($paginator->hasMoreData());

        // Once all pages have been fetched, we will merge them into a single response.
        return $this->mergePaginatedResponses($request, $pageResponses);
    }

    /**
     * Merge multiple responses of a paginated request into a single one.
     *
     * @param Contracts\PaginatedRequestInterface $request The origin request
     * @param Response[] $responses The page responses
     */
    protected function mergePaginatedResponses(PaginatedRequestInterface $request, array $responses): Response
    {
        $contents = [];
        $rawResponses = [];

        // Extract data from each response
        foreach ($responses as $page) {
            $contents[] = $page->getContent();
            $rawResponses = array_merge($rawResponses, $page->getRawResponses());
        }

        // Get rid of potential empty pages
        $contents = array_filter($contents);

        // We need to merge the pages, but because we cannot assume the nature
        // of the content, we need to defer this operation to a dedicated object.
        $mergedContent = $request->getResponsePageMerger()->mergePaginatedContents(...$contents);

        return new Response($request, $mergedContent, $rawResponses);
    }

    /**
     * Execute a batch of asynchronous requests concurrently and return the responses when all received.
     *
     * The response objects are returned in the same order their requests were provided.
     *
     * @param Contracts\RequestInterface[] $requests The batch of requests to execute
     * @return Response[]
     *
     * @throws Exceptions\PaginatedRequestInBatchExecutionException
     */
    public function executeAsyncRequestBatch(array $requests): array
    {
        $responses = [];
        $promises = [];

        foreach ($requests as $i => $request) {
            if ($request instanceof PaginatedRequestInterface && $request->mustBePaginatedAutomatically()) {
                throw new PaginatedRequestInBatchExecutionException();
            }

            $promises[$i] = $this->sendRequest($request, true);
        }

        try {
            $rawResponses = $this->httpLayer->fetchAsyncResponses($promises);
        } catch (AsyncBatchRequestException $e) {
            // Unwrap the actual exception and retrieve the corresponding request.
            $this->handleException($e->getWrappedException(), $requests[$e->getKeyInBatch()]);
        }

        foreach ($rawResponses as $i => $rawResponse) {
            $responses[$i] = $this->responseParser->parse($rawResponse, $requests[$i]);
        }

        return $responses;
    }

    /**
     * Handle an exception thrown by the HTTP layer.
     *
     * @param Contracts\RequestInterface $request The request
     * @return never
     *
     * @throws \Exception
     */
    protected function handleException(Exception $exception, RequestInterface $request): void
    {
        $this->errorHandler->handle($exception, $request);

        // If the error handler did not handle the error, just let it go.
        throw $exception;
    }

    /**
     * Get the number of API requests sent so far.
     */
    public function getRequestCount(): int
    {
        return $this->httpLayer->getRequestCount();
    }

    /**
     * Register a callback to execute before each request.
     *
     * @param callable $callback The callback to execute
     * @param string $id (optional) A unique identifier for the callback
     * @param bool $overwrite (optional) Whether to replace an existing callback having the same identifier
     */
    public function registerPreExecutionHook(callable $callback, string $id = null, bool $overwrite = false): void
    {
        $this->registerHook($this->preExecutionHooks, $callback, $id, $overwrite);
    }

    /**
     * Register a callback to execute after each request.
     *
     * @param callable $callback The callback to execute
     * @param string $id (optional) A unique identifier for the callback
     * @param bool $overwrite (optional) Whether to replace an existing callback having the same identifier
     */
    public function registerPostExecutionHook(callable $callback, string $id = null, bool $overwrite = false): void
    {
        $this->registerHook($this->postExecutionHooks, $callback, $id, $overwrite);
    }

    /**
     * Register a callback in the given set.
     *
     * @param array &$set The set to put the callback in
     * @param callable $callback The callback to execute
     * @param string|null $id A unique identifier for the callback
     * @param bool $overwrite Whether to replace an existing callback having the same identifier
     *
     * @throws \InvalidArgumentException When the identifier is invalid
     * @throws \RuntimeException When the identifier is already taken
     */
    protected function registerHook(array &$set, callable $callback, ?string $id, bool $overwrite): void
    {
        if (! isset($id)) {
            $set[] = $callback;
            return;
        }

        if (is_numeric($id)) {
            throw new \InvalidArgumentException('Callback identifier must not be a numeric string.');
        }

        if (! $overwrite && array_key_exists($id, $set)) {
            throw new \RuntimeException("Callback identifier is not unique: '{$id}'.");
        }

        $set[$id] = $callback;
    }

    /**
     * Deregister an identified callback that was to execute before each request.
     *
     * @param string $id The unique identifier of the callback
     */
    public function deregisterPreExecutionHook(string $id): void
    {
        $this->deregisterHook($this->preExecutionHooks, $id);
    }

    /**
     * Deregister an identified callback that was to execute after each request.
     *
     * @param string $id The unique identifier of the callback
     */
    public function deregisterPostExecutionHook(string $id): void
    {
        $this->deregisterHook($this->postExecutionHooks, $id);
    }

    /**
     * Deregister a callback by ID in the given set.
     *
     * @param array &$set The set to remove the callback from
     * @param string $id The unique identifier of the callback
     *
     * @throws \InvalidArgumentException When the identifier is invalid
     * @throws \RuntimeException When there is no callback with this identifier
     */
    protected function deregisterHook(array &$set, string $id): void
    {
        if (is_numeric($id)) {
            throw new \InvalidArgumentException('Callback identifier must not be a numeric string.');
        }

        if (! array_key_exists($id, $set)) {
            throw new \RuntimeException("No callback is registered with this identifier: '{$id}'.");
        }

        unset($set[$id]);
    }

    /**
     * Execute all registered "pre-execution" callbacks.
     *
     * @param mixed[] ...$args The arguments to pass to the callbacks
     */
    protected function firePreExecutionHooks(mixed ...$args): void
    {
        foreach ($this->preExecutionHooks as $callback) {
            $callback(...$args);
        }
    }

    /**
     * Execute all registered "post-execution" callbacks.
     *
     * @param mixed[] ...$args The arguments to pass to the callbacks
     */
    protected function firePostExecutionHooks(mixed ...$args): void
    {
        foreach ($this->postExecutionHooks as $callback) {
            $callback(...$args);
        }
    }

    /**
     * Register a middleware that will be applied to each request before execution.
     *
     * @param callable $middleware The middleware to register
     */
    public function registerMiddleware(callable $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Apply the registered middlewares to a request.
     *
     * @param Contracts\RequestInterface $request The request being executed
     */
    protected function applyMiddlewaresToRequest(RequestInterface $request): void
    {
        foreach ($this->middlewares as $middleware) {
            $middleware($request);
        }
    }
}

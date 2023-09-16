<?php

namespace Zoho\Crm;

use Closure;
use Exception;
use GuzzleHttp\Psr7\Request;
use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\RequestSenderInterface;
use Zoho\Crm\Contracts\ResponseParserInterface;
use Zoho\Crm\Contracts\ErrorHandlerInterface;
use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Contracts\PaginatedRequestInterface;
use Zoho\Crm\Exceptions\PaginatedRequestInBatchExecutionException;
use Zoho\Crm\Exceptions\AsyncBatchRequestException;
use Zoho\Crm\Support\Helper;

/**
 * The API request processor.
 */
class RequestProcessor
{
    /** @var Contracts\ClientInterface The client to which this processor is attached */
    protected $client;

    /** @var Contracts\RequestSenderInterface The request sender */
    protected $requestSender;

    /** @var Contracts\ResponseParserInterface The response parser */
    protected $responseParser;

    /** @var Contracts\ErrorHandlerInterface The error handler */
    protected $errorHandler;

    /** @var \Closure[] The callbacks to execute before each request */
    protected $preExecutionHooks = [];

    /** @var \Closure[] The callbacks to execute after each request */
    protected $postExecutionHooks = [];

    /** @var callable[] The middlewares to apply to each request before execution */
    protected $middlewares = [];

    /**
     * The constructor.
     *
     * @param Contracts\ClientInterface $client The client to which it is attached
     * @param Contracts\RequestSenderInterface $requestSender The request sender
     * @param Contracts\ResponseParserInterface $responseParser The response parser
     * @param Contracts\ErrorHandlerInterface $errorHandler The error handler
     */
    public function __construct(
        ClientInterface $client,
        RequestSenderInterface $requestSender,
        ResponseParserInterface $responseParser,
        ErrorHandlerInterface $errorHandler
    ) {
        $this->client = $client;
        $this->requestSender = $requestSender;
        $this->responseParser = $responseParser;
        $this->errorHandler = $errorHandler;
    }

    /**
     * Execute a request and get a formal and generic response object.
     *
     * @param Contracts\RequestInterface $request The request to execute
     * @return Response
     */
    public function executeRequest(RequestInterface $request)
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
     * @return \Psr\Http\Message\ResponseInterface|\GuzzleHttp\Promise\PromiseInterface
     */
    protected function sendRequest(RequestInterface $request, bool $async = false)
    {
        // Use a copy of the request, so that all modifications potentially
        // brought by middleware are not affecting the original request.
        $request = $request->copy();

        $this->applyMiddlewaresToRequest($request);

        // Generate a "unique" ID for the request execution
        $execId = $this->generateRandomId();

        $httpRequest = $this->createHttpRequest($request);

        $this->firePreExecutionHooks($request->copy(), $execId);

        if ($async) {
            return $this->requestSender->sendAsync(
                $httpRequest,
                function ($response) use ($request, $execId) {
                    $this->firePostExecutionHooks($request->copy(), $execId);

                    return $response;
                }
            );
        }

        try {
            $response = $this->requestSender->send($httpRequest);
        } catch (Exception $e) {
            $this->handleException($e, $request);
        }

        $this->firePostExecutionHooks($request->copy(), $execId);

        return $response;
    }

    /**
     * Generate a random alpha-numeric string of 16 characters.
     *
     * @return string
     */
    protected function generateRandomId()
    {
        return bin2hex(random_bytes(8));
    }

    /**
     * Create an HTTP request out of an API request.
     *
     * @param Contracts\RequestInterface $request The request
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function createHttpRequest(RequestInterface $request)
    {
        return new Request(
            $request->getHttpMethod(),
            $this->client->getEndpoint() . $request->getUrl(),
            $request->getHeaders(),
            $request->getBody()
        );
    }

    /**
     * Execute a paginated request.
     *
     * @param Contracts\PaginatedRequestInterface $request The request to execute
     * @return Response
     */
    protected function executePaginatedRequest(PaginatedRequestInterface $request)
    {
        $paginator = $request->getPaginator();
        $paginator->fetchAll();

        // Once all pages have been fetched, we will merge them into a single response
        $contents = [];
        $rawContents = [];

        // Extract data from each response
        foreach ($paginator->getResponses() as $page) {
            $contents[] = $page->getContent();
            $rawContents[] = $page->getRawContent();
        }

        // Get rid of potential empty pages
        $contents = array_filter($contents);

        // We need to merge the pages, but because we cannot assume the nature
        // of the content, we need to defer this operation to a dedicated object.
        $mergedContent = $request->getResponsePageMerger()->mergePaginatedContents(...$contents);

        return new Response($request, $mergedContent, $rawContents);
    }

    /**
     * Execute a batch of requests concurrently and get the responses when all received.
     *
     * The response objects are returned in the same order their requests were provided.
     *
     * @param Request[] $requests The batch of requests to execute
     * @return Response[]
     *
     * @throws Exceptions\PaginatedRequestInBatchExecutionException
     */
    public function executeAsyncBatch(array $requests)
    {
        $responses = [];
        $promises = [];

        foreach ($requests as $i => $request) {
            if ($request->mustBePaginatedAutomatically()) {
                throw new PaginatedRequestInBatchExecutionException();
            }

            $promises[$i] = $this->sendRequest($request, true);
        }

        try {
            $rawResponses = $this->requestSender->fetchAsyncResponses($promises);
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
     * Handle an exception thrown by the request sender.
     *
     * @param \Exception $exception
     * @param Contracts\RequestInterface $request The request
     * @return void
     *
     * @throws \Exception
     */
    protected function handleException(Exception $exception, RequestInterface $request)
    {
        $this->errorHandler->handle($exception, $request);

        // If the error handler did not handle the error, just let it go.
        throw $exception;
    }

    /**
     * Get the number of API requests sent so far.
     *
     * @return int
     */
    public function getRequestCount(): int
    {
        return $this->requestSender->getRequestCount();
    }

    /**
     * Register a callback to execute before each request.
     *
     * @param \Closure $callback The callback to execute
     * @return void
     */
    public function registerPreExecutionHook(Closure $callback)
    {
        $this->preExecutionHooks[] = $callback;
    }

    /**
     * Register a callback to execute after each request.
     *
     * @param \Closure $callback The callback to execute
     * @return void
     */
    public function registerPostExecutionHook(Closure $callback)
    {
        $this->postExecutionHooks[] = $callback;
    }

    /**
     * Execute all registered "pre-execution" callbacks.
     *
     * @param mixed[] ...$args The arguments to pass to the callbacks
     * @return void
     */
    protected function firePreExecutionHooks(...$args)
    {
        foreach ($this->preExecutionHooks as $callback) {
            $callback(...$args);
        }
    }

    /**
     * Execute all registered "post-execution" callbacks.
     *
     * @param mixed[] ...$args The arguments to pass to the callbacks
     * @return void
     */
    protected function firePostExecutionHooks(...$args)
    {
        foreach ($this->postExecutionHooks as $callback) {
            $callback(...$args);
        }
    }

    /**
     * Register a middleware that will be applied to each request before execution.
     *
     * @param callable $middleware The middleware to register
     * @return void
     */
    public function registerMiddleware(callable $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Apply the registered middlewares to a request.
     *
     * @param Contracts\RequestInterface $request The request being executed
     * @return void
     */
    protected function applyMiddlewaresToRequest(RequestInterface $request)
    {
        foreach ($this->middlewares as $middleware) {
            $middleware($request);
        }
    }
}

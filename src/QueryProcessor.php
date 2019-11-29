<?php

namespace Zoho\Crm;

use Closure;
use GuzzleHttp\Psr7\Request;
use Zoho\Crm\HttpVerb;
use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Contracts\PaginatedQueryInterface;
use Zoho\Crm\Query;
use Zoho\Crm\Response;
use Zoho\Crm\Exceptions\PaginatedQueryInBatchExecutionException;
use Zoho\Crm\Support\Helper;

/**
 * The query processor.
 */
class QueryProcessor
{
    /** @var \Zoho\Crm\Contracts\ClientInterface The client to which this processor is attached */
    protected $client;

    /** @var RequestSender The request sender */
    protected $requestSender;

    /** @var ResponseParser The response parser */
    protected $responseParser;

    /** @var \Closure[] The callbacks to execute before each query execution */
    protected $preExecutionHooks = [];

    /** @var \Closure[] The callbacks to execute after each query execution */
    protected $postExecutionHooks = [];

    /** @var callable[] The middlewares to apply to each query before execution */
    protected $middlewares = [];

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Contracts\ClientInterface $client The client to which it is attached
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->requestSender = new RequestSender($this->client->preferences());
        $this->responseParser = new ResponseParser();
    }

    /**
     * Execute a query and get a formal and generic response object.
     *
     * @param \Zoho\Crm\Contracts\QueryInterface $query The query to execute
     * @return Response
     */
    public function executeQuery(QueryInterface $query)
    {
        if ($query instanceof PaginatedQueryInterface && $query->isPaginated()) {
            return $this->executePaginatedQuery($query);
        }

        $response = $this->sendQuery($query);

        return $this->responseParser->parse($response, $query);
    }

    /**
     * Process a query and send it, synchronously or asynchronously.
     *
     * If synchronous, the returned value is the response of the API.
     * If asynchronous, the returned value is a promise that needs to be settled afterwards.
     *
     * @param \Zoho\Crm\Contracts\QueryInterface $query The query to process
     * @param bool $async (optional) Whether the resulting request must be asynchronous or not
     * @return \Psr\Http\Message\ResponseInterface|\GuzzleHttp\Promise\PromiseInterface
     */
    protected function sendQuery(QueryInterface $query, bool $async = false)
    {
        // Use a copy of the query, so that all modifications potentially
        // brought by middleware are not affecting the original query.
        $query = $query->copy();

        $this->applyMiddlewaresToQuery($query);

        // Generate a "unique" ID for the query execution
        $execId = $this->generateRandomId();

        $request = $this->createHttpRequest($query);

        $this->firePreExecutionHooks($query->copy(), $execId);

        if ($async) {
            return $this->requestSender->sendAsync(
                $request,
                function ($response) use ($query, $execId) {
                    $this->firePostExecutionHooks($query->copy(), $execId);

                    return $response;
                }
            );
        }

        $response = $this->requestSender->send($request);

        $this->firePostExecutionHooks($query->copy(), $execId);

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
     * Create an HTTP request out of a query.
     *
     * @param \Zoho\Crm\Contracts\QueryInterface $query The query
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function createHttpRequest(QueryInterface $query)
    {
        return new Request(
            $query->getHttpVerb(),
            $this->client->getEndpoint() . $query->getUri(),
            $query->getHeaders(),
            $query->getBody()
        );
    }

    /**
     * Execute a paginated query.
     *
     * @param \Zoho\Crm\Contracts\PaginatedQueryInterface $query The query to execute
     * @return Response
     */
    protected function executePaginatedQuery(PaginatedQueryInterface $query)
    {
        $paginator = $query->getPaginator();
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
        $mergedContent = $query->getResponsePageMerger()->mergePaginatedContents(...$contents);

        return new Response($query, $mergedContent, $rawContents);
    }

    /**
     * Execute a batch of queries concurrently and get the responses when all received.
     *
     * The response objects are returned in the same order their queries were provided.
     *
     * @param Query[] $queries The batch of queries to execute
     * @return Response[]
     *
     * @throws Exceptions\PaginatedQueryInBatchExecutionException
     */
    public function executeAsyncBatch(array $queries)
    {
        $responses = [];
        $promises = [];

        foreach ($queries as $i => $query) {
            if ($query->isPaginated()) {
                throw new PaginatedQueryInBatchExecutionException();
            }

            $promises[$i] = $this->sendQuery($query, true);
        }

        $rawResponses = $this->requestSender->fetchAsyncResponses($promises);

        foreach ($rawResponses as $i => $rawResponse) {
            $responses[$i] = $this->responseParser->parse($rawResponse, $queries[$i]);
        }

        return $responses;
    }

    /**
     * Get the number of API requests sent so far.
     *
     * @return int
     */
    public function getRequestCount()
    {
        return $this->requestSender->getRequestCount();
    }

    /**
     * Register a callback to execute before each query execution.
     *
     * @param \Closure $callback The callback to execute
     * @return void
     */
    public function registerPreExecutionHook(Closure $callback)
    {
        $this->preExecutionHooks[] = $callback;
    }

    /**
     * Register a callback to execute after each query execution.
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
     * Register a middleware that will be applied to each query before execution.
     *
     * @param callable $middleware The middleware to register
     * @return void
     */
    public function registerMiddleware(callable $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Apply the registered middlewares to a query.
     *
     * @param \Zoho\Crm\Contracts\QueryInterface $query The query being executed
     * @return void
     */
    protected function applyMiddlewaresToQuery(QueryInterface $query)
    {
        foreach ($this->middlewares as $middleware) {
            $middleware($query);
        }
    }
}

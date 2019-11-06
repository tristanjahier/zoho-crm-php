<?php

namespace Zoho\Crm;

use Closure;
use GuzzleHttp\Psr7\Request;
use Zoho\Crm\Api\HttpVerb;
use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Contracts\PaginatedQueryInterface;
use Zoho\Crm\Api\Query;
use Zoho\Crm\Api\Response;
use Zoho\Crm\Exceptions\UnsupportedModuleException;
use Zoho\Crm\Exceptions\UnsupportedMethodException;
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

    /** @var ResponseTransformer The response transformer */
    protected $responseTransformer;

    /** @var \Closure[] The callbacks to execute before each query execution */
    protected $preExecutionHooks = [];

    /** @var \Closure[] The callbacks to execute after each query execution */
    protected $postExecutionHooks = [];

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Contracts\ClientInterface $client The client to which it is attached
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->requestSender = new RequestSender($this->client->preferences());
        $this->responseTransformer = new ResponseTransformer();
    }

    /**
     * Execute a query and get a formal and generic response object.
     *
     * @param \Zoho\Crm\Contracts\QueryInterface $query The query to execute
     * @return Api\Response
     */
    public function executeQuery(QueryInterface $query)
    {
        if ($query instanceof PaginatedQueryInterface && $query->isPaginated()) {
            return $this->executePaginatedQuery($query);
        }

        $response = $this->sendQuery($query);

        return $this->responseTransformer->transform($response, $query);
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
        $this->validateQuery($query);

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
     * Validate that a query is valid before sending the request to the API.
     *
     * @param \Zoho\Crm\Contracts\QueryInterface $query The query to validate
     * @return void
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedModuleException
     * @throws \Zoho\Crm\Exceptions\UnsupportedMethodException
     */
    protected function validateQuery(QueryInterface $query)
    {
        // Internal validation logic
        $query->validate();

        // Check if the requested module and method are both supported
        if (! $this->client->supports($query->getModule())) {
            throw new UnsupportedModuleException($query->getModule());
        }

        if (! $this->client->supportsMethod($query->getMethod())) {
            throw new UnsupportedMethodException($query->getMethod());
        }
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
     * Transform a query into an HTTP request.
     *
     * @param \Zoho\Crm\Contracts\QueryInterface $query The query
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function createHttpRequest(QueryInterface $query)
    {
        $headers = [];
        $body = null;
        $queryCopy = $query->copy();

        // Determine the HTTP verb to use from the API method handler
        $httpVerb = $this->client->getMethodHandler($queryCopy->getMethod())->getHttpVerb();

        // Add auth token at the last moment to avoid exposing it in the error log messages
        $queryCopy->param('authtoken', $this->client->getAuthToken());

        // For POST requests, because of the XML data, the parameters size might be very large.
        // For that reason we won't include them in the URL query string, but in the body instead.
        if ($httpVerb === HttpVerb::POST) {
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            $body = (string) $queryCopy->getParameters();
            $queryCopy->resetParameters();
        }

        $fullUrl = $this->client->getEndpoint() . $queryCopy->buildUri();

        return new Request($httpVerb, $fullUrl, $headers, $body);
    }

    /**
     * Execute a paginated query.
     *
     * @param \Zoho\Crm\Contracts\PaginatedQueryInterface $query The query to execute
     * @return Api\Response
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

        // Delegate merging logic to the method handler
        $mergedContent = $this->client
            ->getMethodHandler($query->getMethod())
            ->mergePaginatedContents(...$contents);

        return new Response($query, $mergedContent, $rawContents);
    }

    /**
     * Execute a batch of queries concurrently and get the responses when all received.
     *
     * The response objects are returned in the same order their queries were provided.
     *
     * @param Api\Query[] $queries The batch of queries to execute
     * @return Api\Response[]
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
            $responses[$i] = $this->responseTransformer->transform($rawResponse, $queries[$i]);
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
}

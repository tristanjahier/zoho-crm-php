<?php

namespace Zoho\Crm;

use GuzzleHttp\Psr7\Request;
use Zoho\Crm\Api\HttpVerb;
use Zoho\Crm\Api\Query;
use Zoho\Crm\Api\Response;
use Zoho\Crm\Exceptions\UnsupportedModuleException;
use Zoho\Crm\Exceptions\UnsupportedMethodException;
use Zoho\Crm\Support\Helper;

/**
 * The query processor.
 */
class QueryProcessor
{
    /** @var Client The client to which this processor is attached */
    protected $client;

    /** @var RequestSender The request sender */
    protected $requestSender;

    /** @var ResponseTransformer The response transformer */
    protected $responseTransformer;

    /**
     * The constructor.
     *
     * @param Client $client The client to which it is attached
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->requestSender = new RequestSender($this->client->preferences());
        $this->responseTransformer = new ResponseTransformer();
    }

    /**
     * Execute a given query and get a formal and generic response object.
     *
     * @param Api\Query $query The query to execute
     * @return Api\Response
     */
    public function executeQuery(Query $query)
    {
        if ($query->isPaginated()) {
            return $this->executePaginatedQuery($query);
        }

        $this->validateQuery($query);

        $request = $this->createHttpRequest($query);

        $response = $this->requestSender->send($request);

        return $this->responseTransformer->transform($response, $query);
    }

    /**
     * Validate that a query is valid before sending the request to the API.
     *
     * @param Api\Query $query The query to validate
     * @return void
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedModuleException
     * @throws \Zoho\Crm\Exceptions\UnsupportedMethodException
     */
    private function validateQuery(Query $query)
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
     * Transform a query into an HTTP request.
     *
     * @param Api\Query $query The query
     * @return \GuzzleHttp\Psr7\Request
     */
    private function createHttpRequest(Query $query)
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
     * @param Api\Query $query The query to execute
     * @return Api\Response
     */
    private function executePaginatedQuery(Query $query)
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
     * Get the number of API requests sent so far.
     *
     * @return int
     */
    public function getRequestCount()
    {
        return $this->requestSender->getRequestCount();
    }
}

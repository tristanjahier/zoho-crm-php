<?php

namespace Zoho\Crm;

use GuzzleHttp\Psr7\Request;
use Zoho\Crm\Api\Query;
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

        if (! class_exists(Helper::getMethodClass($query->getMethod()))) {
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
        // Determine the HTTP verb to use from the API method handler
        $methodClass = Helper::getMethodClass($query->getMethod());
        $httpVerb = $methodClass::getHttpVerb();

        // Add auth token at the last moment to avoid exposing it in the error log messages
        $query->param('authtoken', $this->client->getAuthToken());

        $fullUrl = $this->client->getEndpoint() . $query->buildUri();

        return new Request($httpVerb, $fullUrl);
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

        return $paginator->getAggregatedResponse();
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

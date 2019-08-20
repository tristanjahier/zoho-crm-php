<?php

namespace Zoho\Crm;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as HttpRequest;
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

    /** @var \GuzzleHttp\Client The Guzzle client instance to make HTTP requests */
    protected $httpClient;

    /** @var int The number of API requests made */
    protected $requestCount = 0;

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

        $this->setupHttpClient();

        $this->responseTransformer = new ResponseTransformer();
    }

    /**
     * Create and configure the Guzzle client.
     *
     * @return void
     */
    public function setupHttpClient()
    {
        $this->httpClient = new GuzzleClient([
            'base_uri' => $this->client->getEndpoint()
        ]);
    }

    /**
     * Reset the API request counter.
     *
     * @return void
     */
    public function resetRequestCount()
    {
        $this->requestCount = 0;
    }

    /**
     * Get the number of API requests made.
     *
     * @return int
     */
    public function getRequestCount()
    {
        return $this->requestCount;
    }

    /**
     * Execute a given query and get a formal and generic response object.
     *
     * @param Api\Query $query The query to execute
     * @return Api\Response
     *
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public function executeQuery(Query $query)
    {
        if ($query->isPaginated()) {
            return $this->executePaginatedQuery($query);
        }

        $this->validateQuery($query);

        $request = $this->createHttpRequest($query);

        // Perform the HTTP request
        try {
            $response = $this->httpClient->send($request);
            $this->requestCount++;
        } catch (RequestException $e) {
            if ($this->client->preferences()->isEnabled('exception_messages_obfuscation')) {
                // Sometimes the auth token is included in the exception message by Guzzle.
                // This exception message could end up in many "unsafe" places like server logs,
                // error monitoring services, company internal communication etc.
                // For this reason we must remove the auth token from the exception message.

                throw $this->obfuscateExceptionMessage($e);
            }

            throw $e;
        }

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

        return new HttpRequest($httpVerb, $query->buildUri());
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
     * Obfuscate an exception by removing the API auth token from its message.
     *
     * It will actually create a copy of the original exception because
     * exception messages are immutable.
     *
     * @param \GuzzleHttp\Exception\RequestException $e The exception to obfuscate
     * @return \GuzzleHttp\Exception\RequestException
     */
    private function obfuscateExceptionMessage(RequestException $e)
    {
        // If the exception message does not contain sensible data, just let it through.
        if (mb_strpos($e->getMessage(), 'authtoken='.$this->authToken) === false) {
            return $e;
        }

        $safeMessage = str_replace('authtoken='.$this->authToken, 'authtoken=***', $e->getMessage());
        $class = get_class($e);

        return new $class(
            $safeMessage,
            $e->getRequest(),
            $e->getResponse(),
            $e->getPrevious(),
            $e->getHandlerContext()
        );
    }
}

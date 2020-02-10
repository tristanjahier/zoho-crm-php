<?php

namespace Zoho\Crm\V2;

use Closure;
use GuzzleHttp\Psr7\Request;
use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Exceptions\InvalidEndpointException;
use Zoho\Crm\Support\UrlParameters;
use Zoho\Crm\QueryProcessor;
use Zoho\Crm\RequestSender;
use Zoho\Crm\RawQuery;

/**
 * Zoho CRM APIv2 client. Main class of the library.
 *
 * It is the central point for each request to the API of Zoho CRM.
 *
 * @author Tristan Jahier <tristan.jahier@gmail.com>
 *
 * @property-read Records\SubApi $records
 * @property-read Users\SubApi $users
 */
class Client implements ClientInterface
{
    /** @var string The API endpoint used by default */
    const DEFAULT_ENDPOINT = 'https://www.zohoapis.com/crm/v2/';

    /** @var string The API OAuth 2.0 authorization endpoint used by default */
    const DEFAULT_OAUTH_ENDPOINT = 'https://accounts.zoho.com/oauth/v2/';

    /** @var string[] The sub-APIs helpers classes */
    protected static $subApiClasses = [
        'records' => Records\SubApi::class,
        'users' => Users\SubApi::class,
    ];

    /** @var string The OAuth 2.0 client ID */
    protected $oAuthClientId;

    /** @var string The OAuth 2.0 client secret */
    protected $oAuthClientSecret;

    /** @var string The OAuth 2.0 refresh token */
    protected $oAuthRefreshToken;

    /** @var string|null The OAuth 2.0 access token */
    protected $oAuthAccessToken;

    /** @var string The API endpoint base URL (with trailing slash) */
    protected $endpoint = self::DEFAULT_ENDPOINT;

    /** @var string The API OAuth 2.0 authorization endpoint base URL */
    protected $oAuthEndpoint = self::DEFAULT_OAUTH_ENDPOINT;

    /** @var \Zoho\Crm\QueryProcessor The query processor */
    protected $queryProcessor;

    /** @var Preferences The client preferences container */
    protected $preferences;

    /** @var AbstractSubApi[] The sub-APIs helpers */
    protected $subApis = [];

    /** @var \Closure[] The callbacks to execute each time the access token has been refreshed */
    protected $accessTokenRefreshedCallbacks = [];

    /**
     * The constructor.
     *
     * @param string $oAuthClientId The client ID
     * @param string $oAuthClientSecret The client secret
     * @param string $oAuthRefreshToken The refresh token
     * @param string|null $oAuthAccessToken (optional) The access token
     * @param string|null $endpoint (optional) The endpoint base URL
     */
    public function __construct(
        string $oAuthClientId,
        string $oAuthClientSecret,
        string $oAuthRefreshToken,
        string $oAuthAccessToken = null,
        string $endpoint = null)
    {
        $this->oAuthClientId = $oAuthClientId;
        $this->oAuthClientSecret = $oAuthClientSecret;
        $this->oAuthRefreshToken = $oAuthRefreshToken;
        $this->oAuthAccessToken = $oAuthAccessToken;

        if (isset($endpoint)) {
            $this->setEndpoint($endpoint);
        }

        $this->preferences = new Preferences();

        $this->queryProcessor = new QueryProcessor(
            $this,
            new RequestSender(),
            new ResponseParser(),
            new ErrorHandler()
        );

        $this->registerMiddleware(new Middleware\Validation());
        $this->registerMiddleware(new Middleware\Authorization($this));

        $this->attachSubApis();
    }

    /**
     * @inheritdoc
     */
    public function setEndpoint(string $endpoint): void
    {
        // Remove trailing slashes
        $endpoint = rtrim($endpoint, '/');

        if ($endpoint === '') {
            throw new InvalidEndpointException();
        }

        // Make sure the endpoint ends with a single slash
        $this->endpoint = $endpoint . '/';
    }

    /**
     * @inheritdoc
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Get the client preferences container.
     *
     * @return Preferences
     */
    public function preferences(): Preferences
    {
        return $this->preferences;
    }

    /**
     * @inheritdoc
     *
     * @return \Zoho\Crm\Response
     */
    public function executeQuery(QueryInterface $query): ResponseInterface
    {
        return $this->queryProcessor->executeQuery($query);
    }

    /**
     * @inheritdoc
     *
     * @return \Zoho\Crm\Response[]
     */
    public function executeAsyncBatch(array $queries): array
    {
        return $this->queryProcessor->executeAsyncBatch($queries);
    }

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function beforeQueryExecution(Closure $callback): ClientInterface
    {
        $this->queryProcessor->registerPreExecutionHook($callback);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function afterQueryExecution(Closure $callback): ClientInterface
    {
        $this->queryProcessor->registerPostExecutionHook($callback);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRequestCount(): int
    {
        return $this->queryProcessor->getRequestCount();
    }

    /**
     * @inheritdoc
     */
    public function registerMiddleware(callable $middleware): void
    {
        $this->queryProcessor->registerMiddleware($middleware);
    }

    /**
     * Set the API OAuth 2.0 authorization endpoint base URL.
     *
     * It will ensure that there is one slash at the end.
     *
     * @param string $endpoint The endpoint base URL
     * @return void
     */
    public function setAuthorizationEndpoint(string $endpoint): void
    {
        // Remove trailing slashes
        $endpoint = rtrim($endpoint, '/');

        if ($endpoint === '') {
            throw new InvalidEndpointException();
        }

        // Make sure the endpoint ends with a single slash
        $this->oAuthEndpoint = $endpoint . '/';
    }

    /**
     * Get the API OAuth 2.0 authorization endpoint.
     *
     * @return string
     */
    public function getAuthorizationEndpoint(): string
    {
        return $this->oAuthEndpoint;
    }

    /**
     * Set the API OAuth 2.0 access token.
     *
     * @param string|null $accessToken The new access token
     * @return void
     */
    public function setAccessToken(?string $accessToken)
    {
        $this->oAuthAccessToken = $accessToken;
    }

    /**
     * Get the API OAuth 2.0 access token.
     *
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return $this->oAuthAccessToken;
    }

    /**
     * Send a request to the OAuth 2.0 authorization server to get a fresh access token.
     *
     * @return array
     */
    public function refreshAccessToken(): array
    {
        $requestSender = new RequestSender();

        $parameters = new UrlParameters([
            'grant_type' => 'refresh_token',
            'client_id' => $this->oAuthClientId,
            'client_secret' => $this->oAuthClientSecret,
            'refresh_token' => $this->oAuthRefreshToken
        ]);

        $request = new Request('POST', $this->oAuthEndpoint . 'token?' . $parameters);
        $response = $requestSender->send($request);
        $response = json_decode((string) $response->getBody(), true);

        // Save the new access token
        $this->oAuthAccessToken = $response['access_token'] ?? null;

        // Fire the registered callbacks
        foreach ($this->accessTokenRefreshedCallbacks as $callback) {
            $callback($response);
        }

        return $response;
    }

    /**
     * Register a callback to execute each time the access token has been refreshed.
     *
     * @param \Closure $callback The callback to execute
     * @return $this
     */
    public function accessTokenRefreshed(Closure $callback): self
    {
        $this->accessTokenRefreshedCallbacks[] = $callback;

        return $this;
    }

    /**
     * Create a new raw query object.
     *
     * @param string|null $path (optional) The URL path
     * @return RawQuery
     */
    public function newRawQuery(string $path = null)
    {
        return (new RawQuery($this))->setUrl($path);
    }

    /**
     * Attach all sub-APIs helpers.
     *
     * @return void
     */
    protected function attachSubApis(): void
    {
        foreach (static::$subApiClasses as $name => $class) {
            $this->subApis[$name] = new $class($this);
        }
    }

    /**
     * Get all sub-API helpers.
     *
     * @return AbstractSubApi[]
     */
    public function getSubApis(): array
    {
        return $this->subApis;
    }

    /**
     * Get a sub-API helper by name.
     *
     * @param string $name The name of the sub-API
     * @return AbstractSubApi
     */
    public function getSubApi(string $name): AbstractSubApi
    {
        return $this->subApis[$name];
    }

    /**
     * Dynamically retrieve sub-API helpers as client public properties.
     *
     * @param string $name The name of the sub-API
     * @return AbstractSubApi
     */
    public function __get(string $name)
    {
        return $this->getSubApi($name);
    }
}

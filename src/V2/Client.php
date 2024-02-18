<?php

declare(strict_types=1);

namespace Zoho\Crm\V2;

use Closure;
use DateTimeInterface;
use Zoho\Crm\AccessTokenStorage\NoStore;
use Zoho\Crm\Contracts\AccessTokenBrokerInterface;
use Zoho\Crm\Contracts\AccessTokenStoreInterface;
use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\ErrorHandlerInterface;
use Zoho\Crm\Contracts\HttpLayerInterface;
use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Contracts\ResponseParserInterface;
use Zoho\Crm\Exceptions\InvalidEndpointException;
use Zoho\Crm\HttpLayer;
use Zoho\Crm\RawRequest;
use Zoho\Crm\RequestProcessor;
use Zoho\Crm\Support\Helper;

/**
 * Zoho CRM APIv2 client. Main class of the library.
 *
 * It is the central point for each request to the API of Zoho CRM.
 *
 * @author Tristan Jahier <tristan.jahier@gmail.com>
 *
 * @property Records\SubApi $records The Records sub-API helper
 * @property Users\SubApi $users The Users sub-API helper
 */
class Client implements ClientInterface
{
    /**
     * The API endpoint used by default
     *
     * @var string
     */
    public const DEFAULT_ENDPOINT = 'https://www.zohoapis.com/crm/v2/';

    /**
     * The sub-APIs helpers classes
     *
     * @var array<string, class-string>
     */
    protected static array $subApiClasses = [
        'records' => Records\SubApi::class,
        'users' => Users\SubApi::class,
    ];

    /** The access token broker */
    protected AccessTokenBrokerInterface $accessTokenBroker;

    /** The access token store */
    protected AccessTokenStoreInterface $accessTokenStore;

    /** The API endpoint base URL (with trailing slash) */
    protected string $endpoint = self::DEFAULT_ENDPOINT;

    /** The request processor */
    protected RequestProcessor $requestProcessor;

    /** The client preferences container */
    protected Preferences $preferences;

    /**
     * The sub-APIs helpers
     *
     * @var AbstractSubApi[]
     */
    protected array $subApis = [];

    /**
     * The callbacks to execute each time the access token has been refreshed
     *
     * @var Closure[]
     */
    protected array $accessTokenRefreshedCallbacks = [];

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Contracts\AccessTokenBrokerInterface $accessTokenBroker The access token broker
     * @param \Zoho\Crm\Contracts\AccessTokenStoreInterface|null $accessTokenStore (optional) The access token store
     * @param \Zoho\Crm\Contracts\HttpLayerInterface|null $httpLayer (optional) The HTTP layer
     * @param \Zoho\Crm\Contracts\ResponseParserInterface|null $responseParser (optional) The response parser
     * @param \Zoho\Crm\Contracts\ErrorHandlerInterface|null $errorHandler (optional) The error handler
     * @param string|null $endpoint (optional) The endpoint base URL
     */
    public function __construct(
        AccessTokenBrokerInterface $accessTokenBroker,
        AccessTokenStoreInterface $accessTokenStore = null,
        HttpLayerInterface $httpLayer = null,
        ResponseParserInterface $responseParser = null,
        ErrorHandlerInterface $errorHandler = null,
        string $endpoint = null
    ) {
        $this->accessTokenBroker = $accessTokenBroker;
        $this->accessTokenStore = $accessTokenStore ?? new NoStore();

        if (isset($endpoint)) {
            $this->setEndpoint($endpoint);
        }

        $this->preferences = new Preferences();

        $this->requestProcessor = new RequestProcessor(
            $this,
            $httpLayer ?? new HttpLayer(),
            $responseParser ?? new ResponseParser(),
            $errorHandler ?? new ErrorHandler()
        );

        $this->registerMiddleware(new Middleware\Validation());
        $this->registerMiddleware(new Middleware\AccessTokenAutoRefresh($this));
        $this->registerMiddleware(new Middleware\Authorization($this));

        $this->attachSubApis();
    }

    /**
     * @inheritdoc
     */
    public function setEndpoint(string $endpoint): void
    {
        // Make sure the endpoint ends with a single slash
        $endpoint = Helper::finishString($endpoint, '/');

        if ($endpoint === '/') {
            throw new InvalidEndpointException();
        }

        $this->endpoint = $endpoint;
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
    public function executeRequest(RequestInterface $request): ResponseInterface
    {
        return $this->requestProcessor->executeRequest($request);
    }

    /**
     * @inheritdoc
     *
     * @return \Zoho\Crm\Response[]
     */
    public function executeAsyncRequestBatch(array $requests): array
    {
        return $this->requestProcessor->executeAsyncRequestBatch($requests);
    }

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function beforeEachRequest(callable $callback, string $id = null, bool $overwrite = false): static
    {
        $this->requestProcessor->registerPreExecutionHook($callback, $id, $overwrite);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function afterEachRequest(callable $callback, string $id = null, bool $overwrite = false): static
    {
        $this->requestProcessor->registerPostExecutionHook($callback, $id, $overwrite);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return void
     */
    public function cancelBeforeEachRequestCallback(string $id): void
    {
        $this->requestProcessor->deregisterPreExecutionHook($id);
    }

    /**
     * @inheritdoc
     *
     * @return void
     */
    public function cancelAfterEachRequestCallback(string $id): void
    {
        $this->requestProcessor->deregisterPostExecutionHook($id);
    }

    /**
     * @inheritdoc
     */
    public function getRequestCount(): int
    {
        return $this->requestProcessor->getRequestCount();
    }

    /**
     * @inheritdoc
     */
    public function registerMiddleware(callable $middleware): void
    {
        $this->requestProcessor->registerMiddleware($middleware);
    }

    /**
     * Set the API OAuth 2.0 access token and its expiry date.
     *
     * @param string|null $accessToken The new access token
     * @param \DateTimeInterface|null $expiryDate The new expiry date
     * @return void
     */
    public function setAccessToken(?string $accessToken, ?DateTimeInterface $expiryDate): void
    {
        $this->accessTokenStore->setAccessToken($accessToken);
        $this->accessTokenStore->setExpiryDate($expiryDate);
    }

    /**
     * Get the API OAuth 2.0 access token.
     *
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return $this->accessTokenStore->getAccessToken();
    }

    /**
     * Get the API OAuth 2.0 access token expiry date.
     *
     * @return \DateTimeInterface|null
     */
    public function getAccessTokenExpiryDate(): ?DateTimeInterface
    {
        return $this->accessTokenStore->getExpiryDate();
    }

    /**
     * Get the access token store.
     *
     * @return AccessTokenStoreInterface
     */
    public function getAccessTokenStore(): AccessTokenStoreInterface
    {
        return $this->accessTokenStore;
    }

    /**
     * Determine if the access token exists and is still valid.
     *
     * @return bool
     */
    public function accessTokenIsValid(): bool
    {
        return $this->accessTokenStore->isValid();
    }

    /**
     * Get the access token broker.
     *
     * @return \Zoho\Crm\Contracts\AccessTokenBrokerInterface
     */
    public function getAccessTokenBroker(): AccessTokenBrokerInterface
    {
        return $this->accessTokenBroker;
    }

    /**
     * Request a fresh API access token and update the store.
     *
     * @return void
     */
    public function refreshAccessToken(): void
    {
        [$token, $expiryDate] = $this->accessTokenBroker->getAccessTokenWithExpiryDate();

        // Store the new access token
        $this->accessTokenStore->setAccessToken($token);
        $this->accessTokenStore->setExpiryDate($expiryDate);

        if ($this->preferences->isEnabled('access_token_auto_save')) {
            $this->accessTokenStore->save();
        }

        // Fire the registered callbacks
        foreach ($this->accessTokenRefreshedCallbacks as $callback) {
            $callback($token, $expiryDate);
        }
    }

    /**
     * Register a callback to execute each time the access token has been refreshed.
     *
     * @param \Closure $callback The callback to execute
     * @return $this
     */
    public function accessTokenRefreshed(Closure $callback): static
    {
        $this->accessTokenRefreshedCallbacks[] = $callback;

        return $this;
    }

    /**
     * Create a new raw request object.
     *
     * @param string|null $path (optional) The URL path
     * @return RawRequest
     */
    public function newRawRequest(string $path = null): RawRequest
    {
        return (new RawRequest($this))->setUrl($path);
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
    public function __get(string $name): AbstractSubApi
    {
        return $this->getSubApi($name);
    }
}

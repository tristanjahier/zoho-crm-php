<?php

namespace Zoho\Crm\V1;

use Closure;
use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\V1\Modules\AbstractModule;
use Zoho\Crm\V1\Methods\MethodInterface;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\ResponseFormat;
use Zoho\Crm\QueryProcessor;
use Zoho\Crm\RequestSender;
use Zoho\Crm\RawQuery;

/**
 * Zoho CRM API client. Main class of the library.
 *
 * It is the central point for each request to the API of Zoho CRM.
 *
 * @author Tristan Jahier <tristan.jahier@gmail.com>
 *
 * @property-read Modules\Accounts $accounts
 * @property-read Modules\Attachments $attachments
 * @property-read Modules\Calls $calls
 * @property-read Modules\Campaigns $campaigns
 * @property-read Modules\Cases $cases
 * @property-read Modules\ContactRoles $contactRoles
 * @property-read Modules\Contacts $contacts
 * @property-read Modules\Deals $deals
 * @property-read Modules\Events $events
 * @property-read Modules\Info $info
 * @property-read Modules\Invoices $invoices
 * @property-read Modules\Leads $leads
 * @property-read Modules\Notes $notes
 * @property-read Modules\Potentials $potentials
 * @property-read Modules\PotStageHistory $potStageHistory
 * @property-read Modules\PriceBooks $priceBooks
 * @property-read Modules\Products $products
 * @property-read Modules\PurchaseOrders $purchaseOrders
 * @property-read Modules\Quotes $quotes
 * @property-read Modules\SalesOrders $salesOrders
 * @property-read Modules\Solutions $solutions
 * @property-read Modules\Tasks $tasks
 * @property-read Modules\Users $users
 * @property-read Modules\Vendors $vendors
 */
class Client implements ClientInterface
{
    /** @var string The API endpoint used by default */
    const DEFAULT_ENDPOINT = 'https://crm.zoho.com/crm/private/';

    /**
     * @var string The API response format used by default
     * @see ResponseFormat for a list of available values
     */
    const DEFAULT_RESPONSE_FORMAT = ResponseFormat::JSON;

    /** @var string[] The default modules class names */
    protected static $defaultModules = [
        Modules\Accounts::class,
        Modules\Attachments::class,
        Modules\Calls::class,
        Modules\Campaigns::class,
        Modules\Cases::class,
        Modules\ContactRoles::class,
        Modules\Contacts::class,
        Modules\Deals::class,
        Modules\Events::class,
        Modules\Info::class,
        Modules\Invoices::class,
        Modules\Leads::class,
        Modules\Notes::class,
        Modules\Potentials::class,
        Modules\PotStageHistory::class,
        Modules\PriceBooks::class,
        Modules\Products::class,
        Modules\PurchaseOrders::class,
        Modules\Quotes::class,
        Modules\SalesOrders::class,
        Modules\Solutions::class,
        Modules\Tasks::class,
        Modules\Users::class,
        Modules\Vendors::class,
    ];

    protected static $defaultMethodHandlers = [
        Methods\DeleteFile::class,
        Methods\DeleteRecords::class,
        Methods\GetDeletedRecordIds::class,
        Methods\GetFields::class,
        Methods\GetModules::class,
        Methods\GetMyRecords::class,
        Methods\GetRecordById::class,
        Methods\GetRecords::class,
        Methods\GetRelatedRecords::class,
        Methods\GetSearchRecordsByPDC::class,
        Methods\GetUsers::class,
        Methods\InsertRecords::class,
        Methods\SearchRecords::class,
        Methods\UpdateRecords::class,
    ];

    /** @var string The API endpoint base URL (with trailing slash) */
    protected $endpoint = self::DEFAULT_ENDPOINT;

    /** @var string The API authentication token */
    protected $authToken;

    /** @var \Zoho\Crm\QueryProcessor The query processor */
    protected $queryProcessor;

    /** @var Preferences The client preferences container */
    protected $preferences;

    /** @var array The parameters to be added to every API request by default */
    protected $defaultParameters = [
        'scope' => 'crmapi',
        'newFormat' => 1,
        'version' => 2,
        'fromIndex' => QueryPaginator::MIN_INDEX,
        'toIndex' => QueryPaginator::PAGE_MAX_SIZE,
        'sortColumnString' => 'Modified Time',
        'sortOrderString' => 'asc'
    ];

    /** @var Modules\AbstractModule[] The list of Zoho modules available through the API */
    protected $modules = [];

    /** @var string[] An associative array of aliases pointing to real module names */
    protected $moduleAliases = [];

    /** @var Methods\AbstractMethod[] The list of API method handlers */
    protected $methodHandlers = [];

    /**
     * The constructor.
     *
     * @param string|null $authToken (optional) The auth token
     * @param string|null $endpoint (optional) The endpoint base URL
     */
    public function __construct($authToken = null, $endpoint = null)
    {
        // Allow to instanciate a client without an auth token
        if ($authToken !== null) {
            $this->setAuthToken($authToken);
        }

        if (isset($endpoint)) {
            $this->setEndpoint($endpoint);
        }

        $this->preferences = new Preferences();

        $this->queryProcessor = new QueryProcessor(
            $this,
            new RequestSender(),
            new ResponseParser(),
            new ErrorHandler($this->preferences)
        );

        $this->queryProcessor->registerMiddleware(new Middleware\Validation($this));
        $this->queryProcessor->registerMiddleware(new Middleware\Authentication($this));
        $this->queryProcessor->registerMiddleware(new Middleware\XmlDataHandling());

        $this->attachDefaultModules();

        $this->registerDefaultMethodHandlers();
    }

    /**
     * Get the module handlers.
     *
     * @return Modules\AbstractModule[]
     */
    public function modules()
    {
        return $this->modules;
    }

    /**
     * Get the list of supported module names.
     *
     * @return string[]
     */
    public function supportedModules()
    {
        return array_keys($this->modules);
    }

    /**
     * Check if a module is supported by the client.
     *
     * @param string $module The name of the module
     * @return bool
     */
    public function supports($module)
    {
        return in_array($module, $this->supportedModules());
    }

    /**
     * Attach a new module handler to the client.
     *
     * It will take the name of the handler class, check if it extends the base module class,
     * then it will create an instance and bind it to the client.
     * It can also take an alias for the module name.
     *
     * @param string The module class name
     * @param string|null (optional) An alias name for the module
     * @return void
     *
     * @throws Exceptions\ModuleNotFoundException
     * @throws Exceptions\InvalidModuleException
     */
    public function attachModule($module, $alias = null)
    {
        if (! class_exists($module)) {
            throw new Exceptions\ModuleNotFoundException($module);
        }

        if (! in_array(AbstractModule::class, class_parents($module))) {
            throw new Exceptions\InvalidModuleException('Zoho modules must extend ' . AbstractModule::class);
        }

        $this->modules[$module::name()] = new $module($this);

        if (isset($alias)) {
            $this->moduleAliases[$alias] = $module::name();
        }
    }

    /**
     * Attach multiple module handlers to the client.
     *
     * @param string[] $modules An array of module class names.
     * @return void
     *
     * @throws Exceptions\ModuleNotFoundException
     * @throws Exceptions\InvalidModuleException
     */
    public function attachModules(array $modules)
    {
        foreach ($modules as $module) {
            $this->attachModule($module);
        }
    }

    /**
     * Attach the default modules to the client.
     *
     * @return void
     */
    private function attachDefaultModules()
    {
        $this->attachModules(static::$defaultModules);
    }

    /**
     * Get a module handler by API name or by alias.
     *
     * @param string The name of the module
     * @return Modules\AbstractModule
     *
     * @throws Exceptions\UnsupportedModuleException
     */
    public function module($name)
    {
        if ($this->supports($name)) {
            return $this->modules[$name];
        } elseif (isset($this->moduleAliases[$name])) {
            return $this->modules[$this->moduleAliases[$name]];
        }

        throw new Exceptions\UnsupportedModuleException($name);
    }

    /**
     * Get the modules which have an alias, indexed by these aliases.
     *
     * @return Modules\AbstractModule[]
     */
    public function aliasedModules()
    {
        $modules = [];

        foreach ($this->moduleAliases as $alias => $module) {
            $modules[$alias] = $this->modules[$module];
        }

        return $modules;
    }

    /**
     * Register a new API method handler.
     *
     * @param string The method handler class name
     * @return void
     *
     * @throws Exceptions\InvalidMethodHandlerException
     */
    public function registerMethodHandler(string $handler)
    {
        if (! class_exists($handler) || ! in_array(MethodInterface::class, class_implements($handler))) {
            throw new Exceptions\InvalidMethodHandlerException($handler);
        }

        $this->methodHandlers[$handler::name()] = new $handler();
    }

    /**
     * Register multiple API method handlers.
     *
     * @param string[] $handlers The method handlers class names
     * @return void
     *
     * @throws Exceptions\InvalidMethodHandlerException
     */
    public function registerMethodHandlers(array $handlers)
    {
        foreach ($handlers as $handler) {
            $this->registerMethodHandler($handler);
        }
    }

    /**
     * Register all API method handlers supported by default.
     *
     * @return void
     */
    private function registerDefaultMethodHandlers()
    {
        $this->registerMethodHandlers(static::$defaultMethodHandlers);
    }

    /**
     * Get the list of supported API methods.
     *
     * @return string[]
     */
    public function supportedMethods()
    {
        return array_keys($this->methodHandlers);
    }

    /**
     * Check if an API method is supported by the client.
     *
     * @param string $method The name of the method
     * @return bool
     */
    public function supportsMethod(string $method)
    {
        return array_key_exists($method, $this->methodHandlers);
    }

    /**
     * Get an API method handler by name.
     *
     * @param string The name of the method
     * @return Methods\AbstractMethod
     *
     * @throws Exceptions\UnsupportedMethodException
     */
    public function getMethodHandler(string $method)
    {
        if (! $this->supportsMethod($method)) {
            throw new Exceptions\UnsupportedMethodException($method);
        }

        return $this->methodHandlers[$method];
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
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @inheritdoc
     */
    public function setEndpoint(string $endpoint): void
    {
        // Remove trailing slashes
        $endpoint = rtrim($endpoint, '/');

        if ($endpoint === null || $endpoint === '') {
            throw new Exceptions\InvalidEndpointException();
        }

        // Make sure the endpoint ends with a single slash
        $this->endpoint = $endpoint . '/';
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
     * Get the API authentication token.
     *
     * @return string
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * Set the API authentication token.
     *
     * @param string The auth token
     * @return void
     *
     * @throws Exceptions\NullAuthTokenException
     */
    public function setAuthToken($authToken)
    {
        if ($authToken === null || $authToken === '') {
            throw new Exceptions\NullAuthTokenException();
        } else {
            $this->authToken = $authToken;
        }
    }

    /**
     * Get the query processor.
     *
     * @return \Zoho\Crm\QueryProcessor
     */
    public function getQueryProcessor()
    {
        return $this->queryProcessor;
    }

    /**
     * Get the default request parameters.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return $this->defaultParameters;
    }

    /**
     * Set the default request parameters.
     *
     * It will overwrite the parameters array, losing all current values.
     *
     * @param array $params The new default parameters
     * @return void
     */
    public function setDefaultParameters(array $params)
    {
        $this->defaultParameters = $params;
    }

    /**
     * Set a default request parameter.
     *
     * If it already exists, the value will be overwritten.
     *
     * @param string $key The name of the parameter
     * @param mixed $value The value of the parameter
     * @return void
     */
    public function setDefaultParameter($key, $value)
    {
        $this->defaultParameters[$key] = $value;
    }

    /**
     * Remove a default request parameter.
     *
     * @param string $key The name of the parameter
     * @return void
     */
    public function unsetDefaultParameter($key)
    {
        unset($this->defaultParameters[$key]);
    }

    /**
     * Create a new query object.
     *
     * @param string|null $module (optional) The name of the API module
     * @param string|null $method (optional) The name of the API method
     * @param array $params (optional) An array of URL parameters
     * @param bool $autoPaginated (optional) Whether it will be automatically paginated or not
     * @return Query
     */
    public function newQuery($module = null, $method = null, $params = [], $autoPaginated = false)
    {
        return (new Query($this))
            ->format(self::DEFAULT_RESPONSE_FORMAT)
            ->module($module)
            ->method($method)
            ->params($this->defaultParameters)
            ->params($params)
            ->param('authtoken', '_HIDDEN_')
            ->autoPaginated($autoPaginated)
            ->concurrency(
                $autoPaginated && $this->preferences->isEnabled('concurrent_pagination_by_default')
                    ? $this->preferences->get('default_concurrency')
                    : null
            );
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
     * @inheritdoc
     *
     * @throws Exceptions\UnsupportedModuleException
     * @throws Exceptions\UnsupportedMethodException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public function executeQuery(QueryInterface $query): ResponseInterface
    {
        return $this->queryProcessor->executeQuery($query);
    }

    /**
     * @inheritdoc
     *
     * @throws Exceptions\UnsupportedModuleException
     * @throws Exceptions\UnsupportedMethodException
     * @throws Exceptions\PaginatedQueryInBatchExecutionException
     * @throws \GuzzleHttp\Exception\RequestException
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
    public function registerMiddleware(callable $middleware): void
    {
        $this->queryProcessor->registerMiddleware($middleware);
    }

    /**
     * Dynamically retrieve modules as client public properties.
     *
     * The module name needs to be written in camel case.
     * Example: `$client->potentials` instead of `$client->module('Potentials')`.
     * It also works with aliases.
     *
     * @param string $name The name of the module in camel case
     * @return Modules\AbstractModule
     *
     * @throws Exceptions\UnsupportedModuleException
     */
    public function __get($name)
    {
        return $this->module(Helper::inflector()->classify($name));
    }
}

<?php

namespace Zoho\Crm;

use Doctrine\Common\Inflector\Inflector;
use Zoho\Crm\Api\Modules\AbstractModule;
use Zoho\Crm\Api\Methods\MethodInterface;
use Zoho\Crm\Api\Query;

/**
 * Zoho CRM API client. Main class of the library.
 *
 * It is the central point for each request to the API of Zoho CRM.
 *
 * @author Tristan Jahier <tristan.jahier@gmail.com>
 *
 * @property-read Api\Modules\Info $info
 * @property-read Api\Modules\Users $users
 * @property-read Api\Modules\Leads $leads
 * @property-read Api\Modules\Potentials $potentials
 * @property-read Api\Modules\PotStageHistory $potStageHistory
 * @property-read Api\Modules\Calls $calls
 * @property-read Api\Modules\Contacts $contacts
 * @property-read Api\Modules\Vendors $vendors
 * @property-read Api\Modules\Products $products
 * @property-read Api\Modules\Events $events
 * @property-read Api\Modules\Tasks $tasks
 * @property-read Api\Modules\Notes $notes
 * @property-read Api\Modules\Attachments $attachments
 * @property-read Api\Modules\Accounts $accounts
 * @property-read Api\Modules\Deals $deals
 * @property-read Api\Modules\Campaigns $campaigns
 * @property-read Api\Modules\Quotes $quotes
 * @property-read Api\Modules\Cases $cases
 * @property-read Api\Modules\Invoices $invoices
 * @property-read Api\Modules\Solutions $solutions
 * @property-read Api\Modules\PriceBooks $priceBooks
 */
class Client
{
    /** @var string The API endpoint used by default */
    const DEFAULT_ENDPOINT = 'https://crm.zoho.com/crm/private/';

    /**
     * @var string The API response format used by default
     * @see Api\ResponseFormat for a list of available values
     */
    const DEFAULT_RESPONSE_FORMAT = Api\ResponseFormat::JSON;

    /** @var string[] The default modules class names */
    protected static $defaultModules = [
        Api\Modules\Info::class,
        Api\Modules\Users::class,
        Api\Modules\Leads::class,
        Api\Modules\Potentials::class,
        Api\Modules\PotStageHistory::class,
        Api\Modules\Calls::class,
        Api\Modules\Contacts::class,
        Api\Modules\Vendors::class,
        Api\Modules\Products::class,
        Api\Modules\Events::class,
        Api\Modules\Tasks::class,
        Api\Modules\Notes::class,
        Api\Modules\Attachments::class,
        Api\Modules\Accounts::class,
        Api\Modules\Deals::class,
        Api\Modules\Campaigns::class,
        Api\Modules\Quotes::class,
        Api\Modules\Cases::class,
        Api\Modules\Invoices::class,
        Api\Modules\Solutions::class,
        Api\Modules\PriceBooks::class,
    ];

    protected static $defaultMethodHandlers = [
        Api\Methods\DeleteFile::class,
        Api\Methods\DeleteRecords::class,
        Api\Methods\GetDeletedRecordIds::class,
        Api\Methods\GetFields::class,
        Api\Methods\GetModules::class,
        Api\Methods\GetMyRecords::class,
        Api\Methods\GetRecordById::class,
        Api\Methods\GetRecords::class,
        Api\Methods\GetRelatedRecords::class,
        Api\Methods\GetSearchRecordsByPDC::class,
        Api\Methods\GetUsers::class,
        Api\Methods\InsertRecords::class,
        Api\Methods\SearchRecords::class,
        Api\Methods\UpdateRecords::class,
    ];

    /** @var string The API endpoint (base URI with trailing slash) */
    protected $endpoint = self::DEFAULT_ENDPOINT;

    /** @var string The API authentication token */
    protected $authToken;

    /** @var QueryProcessor The query processor */
    protected $queryProcessor;

    /** @var Preferences The client preferences container */
    protected $preferences;

    /** @var array The parameters to be added to every API request by default */
    protected $defaultParameters = [
        'scope' => 'crmapi',
        'newFormat' => 1,
        'version' => 2,
        'fromIndex' => Api\QueryPaginator::MIN_INDEX,
        'toIndex' => Api\QueryPaginator::PAGE_MAX_SIZE,
        'sortColumnString' => 'Modified Time',
        'sortOrderString' => 'asc'
    ];

    /** @var Api\Modules\AbstractModule[] The list of Zoho modules available through the API */
    protected $modules = [];

    /** @var string[] An associative array of aliases pointing to real module names */
    protected $moduleAliases = [];

    /** @var Api\Methods\AbstractMethod[] The list of API method handlers */
    protected $methodHandlers = [];

    /**
     * The constructor.
     *
     * @param string|null $authToken (optional) The auth token
     * @param string|null $endpoint (optional) The endpoint URI
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

        $this->queryProcessor = new QueryProcessor($this);

        $this->attachDefaultModules();

        $this->registerDefaultMethodHandlers();
    }

    /**
     * Get the module handlers.
     *
     * @return Api\Modules\AbstractModule[]
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
     * @return Api\Modules\AbstractModule
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
     * @return Api\Modules\AbstractModule[]
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
     * @return Api\Methods\AbstractMethod
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
     * Get the number of API requests made by the client.
     *
     * @return int
     */
    public function getRequestCount()
    {
        return $this->queryProcessor->getRequestCount();
    }

    /**
     * Get the API endpoint.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set the API endpoint.
     *
     * It will ensure that there is one slash at the end.
     *
     * @param string $endpoint The endpoint URI
     * @return void
     *
     * @throws Exceptions\InvalidEndpointException
     */
    public function setEndpoint($endpoint)
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
    public function preferences()
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
     * @return QueryProcessor
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
     * @param bool $paginated (optional) Whether to fetch multiple pages or not
     * @return Api\Query
     */
    public function newQuery($module = null, $method = null, $params = [], $paginated = false)
    {
        return (new Query($this))
            ->format(self::DEFAULT_RESPONSE_FORMAT)
            ->module($module)
            ->method($method)
            ->params($this->defaultParameters)
            ->params($params)
            ->param('authtoken', '_HIDDEN_')
            ->paginated($paginated);
    }

    /**
     * Execute a given query and get a formal and generic response object.
     *
     * @param Api\Query $query The query to execute
     * @return Api\Response
     *
     * @throws Exceptions\UnsupportedModuleException
     * @throws Exceptions\UnsupportedMethodException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public function executeQuery(Query $query)
    {
        return $this->queryProcessor->executeQuery($query);
    }

    /**
     * Dynamically retrieve modules as client public properties.
     *
     * The module name needs to be written in camel case.
     * Example: `$client->potentials` instead of `$client->module('Potentials')`.
     * It also works with aliases.
     *
     * @param string $name The name of the module in camel case
     * @return Api\Modules\AbstractModule
     *
     * @throws Exceptions\UnsupportedModuleException
     */
    public function __get($name)
    {
        return $this->module(Inflector::classify($name));
    }
}

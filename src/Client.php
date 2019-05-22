<?php

namespace Zoho\Crm;

use Zoho\Crm\Support\Helper;
use Zoho\Crm\Api\Modules\AbstractModule;
use Zoho\Crm\Api\Query;
use Doctrine\Common\Inflector\Inflector;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

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
    protected static $default_modules = [
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
    ];

    /** @var string The API endpoint (base URI with trailing slash) */
    protected $endpoint = self::DEFAULT_ENDPOINT;

    /** @var string The API authentication token */
    protected $auth_token;

    /** @var \GuzzleHttp\Client The Guzzle client instance to make HTTP requests */
    protected $http_client;

    /** @var int The number of API requests made by the client */
    protected $request_count = 0;

    /** @var Preferences The client preferences container */
    protected $preferences;

    /** @var array The parameters to be added to every API request by default */
    protected $default_parameters = [
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
    protected $module_aliases = [];

    /**
     * The constructor.
     *
     * @param string|null $auth_token (optional) The auth token
     * @param string|null $endpoint (optional) The endpoint URI
     */
    public function __construct($auth_token = null, $endpoint = null)
    {
        // Allow to instanciate a client without an auth token
        if ($auth_token !== null) {
            $this->setAuthToken($auth_token);
        }

        if (isset($endpoint)) {
            $this->setEndpoint($endpoint);
        }

        $this->setupHttpClient();

        $this->preferences = new Preferences();

        $this->attachDefaultModules();
    }

    /**
     * Create and configure the Guzzle client.
     *
     * @return void
     */
    private function setupHttpClient()
    {
        $this->http_client = new GuzzleClient([
            'base_uri' => $this->endpoint
        ]);
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
            $this->module_aliases[$alias] = $module::name();
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
        $this->attachModules(static::$default_modules);
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
        } elseif (isset($this->module_aliases[$name])) {
            return $this->modules[$this->module_aliases[$name]];
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

        foreach ($this->module_aliases as $alias => $module) {
            $modules[$alias] = $this->modules[$module];
        }

        return $modules;
    }

    /**
     * Reset the API request counter.
     *
     * @return void
     */
    public function resetRequestCount()
    {
        $this->request_count = 0;
    }

    /**
     * Get the number of API requests made by the client.
     *
     * @return int
     */
    public function getRequestCount()
    {
        return $this->request_count;
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

        // Re-create the HTTP client because the base URI has changed
        $this->setupHttpClient();
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
        return $this->auth_token;
    }

    /**
     * Set the API authentication token.
     *
     * @param string The auth token
     * @return void
     *
     * @throws Exceptions\NullAuthTokenException
     */
    public function setAuthToken($auth_token)
    {
        if ($auth_token === null || $auth_token === '') {
            throw new Exceptions\NullAuthTokenException();
        } else {
            $this->auth_token = $auth_token;
        }
    }

    /**
     * Get the default request parameters.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return $this->default_parameters;
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
        $this->default_parameters = $params;
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
        $this->default_parameters[$key] = $value;
    }

    /**
     * Remove a default request parameter.
     *
     * @param string $key The name of the parameter
     * @return void
     */
    public function unsetDefaultParameter($key)
    {
        unset($this->default_parameters[$key]);
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
            ->params($this->default_parameters)
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
        if ($query->isPaginated()) {
            $paginator = $query->getPaginator();
            $paginator->fetchAll();
            return $paginator->getAggregatedResponse();
        }

        $query->validate();

        // Check if the requested module and method are both supported
        if (! $this->supports($query->getModule())) {
            throw new Exceptions\UnsupportedModuleException($query->getModule());
        }

        if (! class_exists($method_class = Helper::getMethodClass($query->getMethod()))) {
            throw new Exceptions\UnsupportedMethodException($query->getMethod());
        }

        // Determine the HTTP verb to use based on the API method
        $http_verb = $method_class::getHttpVerb();

        // Add auth token at the last moment to avoid exposing it in the error log messages
        $query->param('authtoken', $this->auth_token);

        // Perform the HTTP request
        try {
            $response = $this->http_client->request($http_verb, $query->buildUri());
            $this->request_count++;
        } catch (RequestException $e) {
            if ($this->preferences->isEnabled('exception_messages_obfuscation')) {
                // Sometimes the auth token is included in the exception message by Guzzle.
                // This exception message could end up in many "unsafe" places like server logs,
                // error monitoring services, company internal communication etc.
                // For this reason we must remove the auth token from the exception message.

                throw $this->obfuscateExceptionMessage($e);
            }

            throw $e;
        }

        // Clean the response
        $raw_content = $response->getBody()->getContents();
        $content = Api\ResponseParser::clean($query, $raw_content);
        $response = new Api\Response($query, $content, $raw_content);

        return $response;
    }

    /**
     * Execute a given query and get a result adapted to the nature of the query.
     *
     * For example, a query which expects a single record will return an entity object,
     * or null if there is no data. A query which expects multiple records will return
     * a collection.
     *
     * @param Api\Query $query The query to execute
     * @return mixed
     *
     * @throws Exceptions\UnsupportedModuleException
     * @throws Exceptions\UnsupportedMethodException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public function getQueryResults(Query $query)
    {
        $response = $query->execute();

        $module = $this->module($query->getModule());

        if ($response->isConvertibleToEntity() && $module->hasAssociatedEntity()) {
            if ($response->hasMultipleRecords()) {
                return $response->toEntityCollection();
            } else {
                return $response->toEntity();
            }
        }

        return $response->getContent();
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
        if (mb_strpos($e->getMessage(), 'authtoken='.$this->auth_token) === false) {
            return $e;
        }

        $safe_message = str_replace('authtoken='.$this->auth_token, 'authtoken=***', $e->getMessage());
        $class = get_class($e);

        return new $class(
            $safe_message,
            $e->getRequest(),
            $e->getResponse(),
            $e->getPrevious(),
            $e->getHandlerContext()
        );
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

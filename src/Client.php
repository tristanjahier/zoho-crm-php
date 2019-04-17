<?php

namespace Zoho\Crm;

use Zoho\Crm\Support\Helper;
use Zoho\Crm\Api\Modules\AbstractModule;
use Zoho\Crm\Api\Query;
use Doctrine\Common\Inflector\Inflector;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class Client
{
    const DEFAULT_ENDPOINT = 'https://crm.zoho.com/crm/private/';

    const DEFAULT_FORMAT = Api\ResponseFormat::JSON;

    private static $default_modules = [
        Api\Modules\Info::class,
        Api\Modules\Users::class,
        Api\Modules\Leads::class,
        Api\Modules\Potentials::class,
        Api\Modules\PotStageHistory::class,
        Api\Modules\Calls::class,
        Api\Modules\Contacts::class,
        Api\Modules\Products::class,
        Api\Modules\Events::class,
        Api\Modules\Tasks::class,
        Api\Modules\Notes::class,
        Api\Modules\Attachments::class,
    ];

    private $endpoint = self::DEFAULT_ENDPOINT;

    private $auth_token;

    private $http_client;

    private $request_count = 0;

    private $preferences;

    private $default_parameters = [
        'scope' => 'crmapi',
        'newFormat' => 1,
        'version' => 2,
        'fromIndex' => Api\QueryPaginator::MIN_INDEX,
        'toIndex' => Api\QueryPaginator::PAGE_MAX_SIZE,
        'sortColumnString' => 'Modified Time',
        'sortOrderString' => 'asc'
    ];

    private $modules = [];

    private $module_aliases = [];

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

    private function setupHttpClient()
    {
        $this->http_client = new GuzzleClient([
            'base_uri' => $this->endpoint
        ]);
    }

    public function modules()
    {
        return $this->modules;
    }

    public function supportedModules()
    {
        return array_keys($this->modules);
    }

    public function supports($module)
    {
        return in_array($module, $this->supportedModules());
    }

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

    public function attachModules(array $modules)
    {
        foreach ($modules as $module) {
            $this->attachModule($module);
        }
    }

    private function attachDefaultModules()
    {
        $this->attachModules(self::$default_modules);
    }

    public function module($name)
    {
        if ($this->supports($name)) {
            return $this->modules[$name];
        } elseif (isset($this->module_aliases[$name])) {
            return $this->modules[$this->module_aliases[$name]];
        }

        throw new Exceptions\UnsupportedModuleException($name);
    }

    public function aliasedModules()
    {
        $modules = [];

        foreach ($this->module_aliases as $alias => $module) {
            $modules[$alias] = $this->modules[$module];
        }

        return $modules;
    }

    public function resetRequestCount()
    {
        $this->request_count = 0;
    }

    public function getRequestCount()
    {
        return $this->request_count;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

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

    public function preferences()
    {
        return $this->preferences;
    }

    public function getAuthToken()
    {
        return $this->auth_token;
    }

    public function setAuthToken($auth_token)
    {
        if ($auth_token === null || $auth_token === '')
            throw new Exceptions\NullAuthTokenException();
        else
            $this->auth_token = $auth_token;
    }

    public function getDefaultParameters()
    {
        return $this->default_parameters;
    }

    public function setDefaultParameters(array $params)
    {
        $this->default_parameters = $params;
    }

    public function setDefaultParameter($key, $value)
    {
        $this->default_parameters[$key] = $value;
    }

    public function unsetDefaultParameter($key)
    {
        unset($this->default_parameters[$key]);
    }

    public function newQuery($module = null, $method = null, $params = [], $paginated = false)
    {
        return (new Query($this))
            ->format(self::DEFAULT_FORMAT)
            ->module($module)
            ->method($method)
            ->params($this->default_parameters)
            ->params($params)
            ->param('authtoken', '_HIDDEN_')
            ->paginated($paginated);
    }

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

    public function __get($name)
    {
        return $this->module(Inflector::classify($name));
    }
}

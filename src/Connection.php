<?php

namespace Zoho\Crm;

use Zoho\Crm\Support\Helper;
use Zoho\Crm\Api\Modules\AbstractModule;
use Zoho\Crm\Api\Query;
use Doctrine\Common\Inflector\Inflector;
use GuzzleHttp\Client as GuzzleClient;

class Connection
{
    const DEFAULT_ENDPOINT = 'https://crm.zoho.com/crm/private/';

    const DEFAULT_FORMAT = Api\ResponseFormat::JSON;

    private static $default_modules = [
        'Info',
        'Users',
        'Leads',
        'Potentials',
        'PotStageHistory',
        'Calls',
        'Contacts',
        'Products',
        'Events',
        'Tasks',
        'Notes',
        'Attachments',
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

    public function __construct($auth_token = null)
    {
        $this->http_client = new GuzzleClient([
            'base_uri' => $this->endpoint
        ]);

        // Allow to instanciate a connection without an auth token
        if ($auth_token !== null) {
            $this->setAuthToken($auth_token);
        }

        $this->preferences = new Preferences();

        $this->attachDefaultModules();
    }

    public static function defaultModules()
    {
        return self::$default_modules;
    }

    public function supportedModules()
    {
        return array_keys($this->modules);
    }

    public function supports($module)
    {
        return in_array($module, $this->supportedModules());
    }

    public function attachModule($module)
    {
        if (! class_exists($module)) {
            throw new Exceptions\ModuleNotFoundException($module);
        }

        if (! in_array(AbstractModule::class, class_parents($module))) {
            throw new Exceptions\InvalidModuleException('Zoho modules must extend ' . AbstractModule::class);
        }

        $this->modules[$module::name()] = $module;
        $parameterized_name = Inflector::tableize($module::name());
        return $this->{$parameterized_name} = new $module($this);
    }

    public function attachModules(array $modules)
    {
        foreach ($modules as $module) {
            $this->attachModule($module);
        }
    }

    private function attachDefaultModules()
    {
        foreach (self::$default_modules as $module) {
            $this->attachModule(Helper::getModuleClass($module));
        }
    }

    public function moduleClass($name)
    {
        return isset($this->modules[$name]) ? $this->modules[$name] : null;
    }

    public function module($module)
    {
        return $this->{Inflector::tableize($module)};
    }

    public function resetRequestCount()
    {
        $this->request_count = 0;
    }

    public function getRequestCount()
    {
        return $this->request_count;
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
        if ($this->preferences->get('validate_queries')) {
            $module = $query->getModule();
            $method = $query->getMethod();

            if (! $this->supports($module)) {
                throw new Exceptions\UnsupportedModuleException($module);
            } elseif (! $this->module($module)->supports($method)) {
                throw new Exceptions\UnsupportedMethodException($module, $method);
            }
        }

        // Add auth token after validation to avoid exposing it in the error log messages
        $query->param('authtoken', $this->auth_token);

        // Determine the HTTP verb to use based on the API method
        $method_class = Helper::getMethodClass($query->getMethod());
        $http_verb = $method_class::getHttpVerb();

        // Perform the HTTP request
        $response = $this->http_client->request($http_verb, $query->buildUri());
        $this->request_count++;

        // Clean the response
        $raw_content = $response->getBody()->getContents();
        $content = Api\ResponseParser::clean($query, $raw_content);
        $response = new Api\Response($query, $content, $raw_content);

        return $response;
    }

    public function getQueryResults(Query $query)
    {
        $response = $query->execute();

        $module_class = $this->moduleClass($query->getModule());

        if ($response->isConvertibleToEntity() && $module_class::hasAssociatedEntity()) {
            if ($response->hasMultipleRecords()) {
                return $response->toEntityCollection();
            } else {
                return $response->toEntity();
            }
        }

        return $response->getContent();
    }
}

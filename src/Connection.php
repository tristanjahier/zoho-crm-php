<?php

namespace Zoho\CRM;

use Zoho\CRM\ResponseMode;
use Zoho\CRM\Entities\AbstractEntity;
use Zoho\CRM\Entities\Collection;
use Zoho\CRM\Api\Modules\AbstractModule;
use Doctrine\Common\Inflector\Inflector;

class Connection
{
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

    private $auth_token;

    private $preferences;

    private $default_parameters = [
        'scope' => 'crmapi',
        'newFormat' => 1,
        'version' => 2,
        'fromIndex' => Api\RequestPaginator::MIN_INDEX,
        'toIndex' => Api\RequestPaginator::PAGE_MAX_SIZE,
        'sortColumnString' => 'Modified Time',
        'sortOrderString' => 'asc'
    ];

    private $modules = [];

    public function __construct($auth_token = null)
    {
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
            throw new Exception\ModuleNotFoundException($module);
        }

        if (! in_array(AbstractModule::class, class_parents($module))) {
            throw new Exception\InvalidModuleException('Zoho modules must extend ' . AbstractModule::class);
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
            $this->attachModule(getModuleClassName($module));
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
            throw new Exception\NullAuthTokenException();
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

    public function request($module, $method, array $params = [], $pagination = false, $format = Api\ResponseFormat::JSON)
    {
        if ($this->preferences->getValidateRequests()) {
            // Check if the requested module and method are both supported
            if (! $this->supports($module)) {
                throw new Exception\UnsupportedModuleException($module);
            } elseif (! $this->module($module)->supports($method)) {
                throw new Exception\UnsupportedMethodException($module, $method);
            }
        }

        // Extend default parameters with the current auth token, and the user-defined parameters
        $url_parameters = (new Api\UrlParameters($this->default_parameters))
                              ->extend(['authtoken' => $this->auth_token])
                              ->extend($params);

        // Edge case for 'maxModifiedTime' parameter which is not part of the Zoho API
        $max_modified_time = $url_parameters->pull('maxModifiedTime');

        // Determine the HTTP verb (GET or POST) to use based on the API method
        $method_class = getMethodClassName($method);
        $http_verb = $method_class::getHttpVerb();

        // Build a request object which encapsulates everything
        $request = new Api\Request($this, $format, $module, $method, $url_parameters, $http_verb);

        $response = null;

        if ($pagination) {
            // If pagination is requested or required, let a paginator handle the request
            $paginator = new Api\RequestPaginator($request);

            if (isset($max_modified_time)) {
                $paginator->setMaxModifiedTime($max_modified_time);
            }

            // According to preferences, we may automatically fetch all for the user
            if ($this->preferences->getAutoFetchPaginatedRequests()) {
                $paginator->fetchAll();
                $response = $paginator->getAggregatedResponse();
            } else {
                return $paginator;
            }
        } else {
            // Send request to Zoho, parse, then finally clean its response
            $raw_data = Api\RequestLauncher::fire($request);
            $clean_data = Api\ResponseParser::clean($request, $raw_data);
            $response = new Api\Response($request, $raw_data, $clean_data);
        }

        return $this->preferredResponse($response);
    }

    private function preferredResponse(Api\Response $response)
    {
        if ($this->preferences->getResponseMode() === ResponseMode::WRAPPED) {
            return $response;
        }

        $module_class = $response->getRequest()->getModuleClass();

        // If the developer prefers entity objects rather than arrays
        // AND the data is convertible into entities
        // AND the module has an associated entity class
        $convert_to_entity = $this->preferences->getRecordsAsEntities() &&
                             $response->isConvertibleToEntity() &&
                             isset($module_class) &&
                             $module_class::hasAssociatedEntity();

        if ($convert_to_entity) {
            if ($response->hasMultipleRecords()) {
                return Collection::createFromResponse($response);
            } else {
                return AbstractEntity::createFromResponse($response);
            }
        }

        return $response->getContent();
    }
}

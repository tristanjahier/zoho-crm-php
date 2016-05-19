<?php

namespace Zoho\CRM;

use Zoho\CRM\ClientResponseMode;
use Doctrine\Common\Inflector\Inflector;

class Client
{
    private $auth_token;

    private $preferences;

    private static $supported_modules = [
        'Info',
        'Users',
        'Leads',
        'Potentials',
        'Calls',
        'Contacts',
        'Products'
    ];

    private $default_parameters = [
        'scope' => 'crmapi',
        'newFormat' => 1,
        'version' => 2,
        'fromIndex' => Api\RequestPaginator::MIN_INDEX,
        'toIndex' => Api\RequestPaginator::PAGE_MAX_SIZE
    ];

    public function __construct($auth_token)
    {
        $this->setAuthToken($auth_token);

        $this->preferences = new ClientPreferences();

        $this->registerModules();
    }

    public static function supportedModules()
    {
        return self::$supported_modules;
    }

    public static function supports($module)
    {
        return in_array($module, self::$supported_modules);
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

    private function registerModules()
    {
        foreach (self::$supported_modules as $module) {
            $parameterized_module = Inflector::tableize($module);
            $class_name = getModuleClassName($module);
            if (class_exists($class_name)) {
                $this->{$parameterized_module} = new $class_name($this);
            } else {
                throw new Exception\ModuleNotFoundException("Module $class_name not found.");
            }
        }
    }

    public function module($module)
    {
        return $this->{Inflector::tableize($module)};
    }

    public function request($module, $method, array $params = [], $pagination = false, $format = Api\ResponseFormat::JSON)
    {
        // Check if the requested module and method are both supported
        if (!$this->supports($module)) {
            throw new Exception\UnsupportedModuleException($module);
        } elseif (!$this->module($module)->supports($method)) {
            throw new Exception\UnsupportedMethodException($module, $method);
        }

        // Extend default parameters with the current auth token, and the user-defined parameters
        $url_parameters = (new Api\UrlParameters($this->default_parameters))
                              ->extend(['authtoken' => $this->auth_token])
                              ->extend($params);

        // Determine the HTTP verb (GET or POST) to use based on the API method
        $http_verb = getMethodClassName($method)::getHttpVerb();

        // Build a request object which encapsulates everything
        $request = new Api\Request($format, $module, $method, $url_parameters, $http_verb);

        $response = null;

        if ($pagination) {
            // If pagination is requested or required, let a paginator handle the request
            $paginator = new Api\RequestPaginator($request);

            // According to preferences, we may automatically fetch all for the user
            if ($this->preferences->getAutoFetchPaginatedRequests()) {
                $paginator->fetchAll();
                $response = $paginator->getAggregatedResponse();
            } else {
                return $paginator;
            }
        } else {
            // Send the request to the Zoho API, parse, then finally clean its response
            $raw_data = Api\RequestLauncher::fire($request);
            $clean_data = Api\ResponseParser::clean($request, $raw_data);
            $response = new Api\Response($request, $raw_data, $clean_data);
        }

        // Transform the response according to preferences
        if ($this->preferences->getResponseMode() === ClientResponseMode::DIRECT) {
            // If user prefers Entity objects rather than arrays,
            // AND if the response contains records, convert them to entities
            if ($this->preferences->getRecordsAsEntities() && $response->containsRecords()) {
                return $response->toEntity();
            } else {
                return $response->getContent();
            }
        }

        return $response;
    }
}

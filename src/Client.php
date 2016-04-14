<?php

namespace Zoho\CRM;

class Client
{
    private $auth_token;

    private $preferences;

    private $supported_modules = [
        'Info',
        'Leads',
        'Users'
    ];

    private $default_parameters = [
        'scope' => 'crmapi',
        'newFormat' => 1,
        'version' => 2
    ];

    public function __construct($auth_token)
    {
        $this->setAuthToken($auth_token);

        $this->preferences = new Core\ClientPreferences();

        $this->registerModules();
    }

    public function getSupportedModules()
    {
        return $this->supported_modules;
    }

    public function supports($module)
    {
        return in_array($module, $this->supported_modules);
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
        foreach ($this->supported_modules as $module) {
            $parameterized_module = toSnakeCase($module);
            $class_name = "\\Zoho\\CRM\\Modules\\$module";
            if (class_exists($class_name)) {
                $this->{$parameterized_module} = new $class_name($this);
            } else {
                throw new Exception\ModuleNotFoundException("Module $class_name not found.");
            }
        }
    }

    public function request($module, $method, array $params = [], $format = Core\ResponseFormat::JSON)
    {
        if (!$this->supports($module)) {
            throw new Exception\UnsupportedModuleException($module);
        } elseif (!$this->{toSnakeCase($module)}->supports($method)) {
            throw new Exception\UnsupportedMethodException($module, $method);
        }

        $url_parameters = (new Core\UrlParameters($this->default_parameters))
                              ->extend(['authtoken' => $this->auth_token])
                              ->extend($params);

        $request = new Core\Request($format, $module, $method, $url_parameters);
        $raw_data = Core\ApiRequestLauncher::fire($request);
        $clean_data = Core\ApiResponseParser::getData($request, $raw_data);

        return new Core\Response($request, $raw_data, $clean_data);
    }
}

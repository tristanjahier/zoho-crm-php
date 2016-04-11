<?php

namespace Zoho\CRM;

class Client
{
    const API_BASE_URI = 'https://crm.zoho.com/crm/private/';

    private $http_client;

    private $auth_token;

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

        $this->http_client = new \GuzzleHttp\Client([
            'base_uri' => self::API_BASE_URI
        ]);

        $this->registerModules();
    }

    public function getSupportedModules()
    {
        return $this->supported_modules;
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
            $parameterized_module = lcfirst($module);
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
        $url_parameters = (new Core\UrlParameters($this->default_parameters))
                              ->extend(['authtoken' => $this->auth_token])
                              ->extend($params);

        $request_uri = "$format/$module/$method?$url_parameters";
        $response = $this->http_client->get($request_uri)->getBody()->getContents();

        $clean_data = Core\ApiResponseParser::getData($response, $module, $method, $format);
        return new Core\Response($this, $module, $method, $format, $response, $clean_data);
    }
}

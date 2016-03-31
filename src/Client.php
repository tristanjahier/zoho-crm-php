<?php

namespace Zoho\CRM;

class Client
{
    const API_BASE_URI = 'https://crm.zoho.com/crm/private/';

    private $http_client;

    private $auth_token;

    private $modules = [
        'Leads'
    ];

    public function __construct($auth_token)
    {
        $this->auth_token = $auth_token;

        $this->http_client = new \GuzzleHttp\Client([
            'base_uri' => self::API_BASE_URI
        ]);

        $this->registerModules();
    }

    public function getSupportedModules()
    {
        return $this->modules;
    }

    public function getAuthToken()
    {
        return $this->auth_token;
    }

    private function registerModules()
    {
        foreach ($this->modules as $module) {
            $parameterized_module = lcfirst($module);
            $class_name = "\\Zoho\\CRM\\Modules\\$module";
            if (class_exists($class_name)) {
                $this->{$parameterized_module} = new $class_name($this);
            } else {
                throw new Exception\ModuleNotFoundException("Module $class_name not found");
            }
        }
    }

    public function request($module, $method, array $params = [], $format = Core\ResponseFormat::JSON)
    {
        if ($this->auth_token === null)
            return;

        $params_string = '?authtoken=' . $this->auth_token;
        foreach ($params as $key => $value) {
            $params_string .= '&' . $key . '=' . urlencode($value);
        }

        $request_uri = $format . '/' . $module . '/' . $method . $params_string;
        $response = $this->http_client->get($request_uri);
        $response_body = $response->getBody()->getContents();

        return Core\ResponseParser::parse($response_body, $format);
    }
}

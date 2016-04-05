<?php

namespace Zoho\CRM\Core;

use Zoho\CRM\Client as ZohoClient;

class Response
{
    private $client;

    private $module_name;

    private $method;

    private $format;

    private $raw_data;

    private $data;

    public function __construct(ZohoClient $client, $module_name, $method, $format, $raw_data, $data)
    {
        $this->client = $client;
        $this->module_name = $module_name;
        $this->method = $method;
        $this->format = $format;
        $this->raw_data = $raw_data;
        $this->data = $data;
    }

    public function getModuleName()
    {
        return $this->module_name;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getRawData()
    {
        return $this->raw_data;
    }

    public function getData()
    {
        return $this->data;
    }
}

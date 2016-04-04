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

    public function __construct(ZohoClient $client, $module_name, $method, $format, $raw_data)
    {
        $this->client = $client;
        $this->module_name = $module_name;
        $this->method = $method;
        $this->format = $format;
        $this->raw_data = $raw_data;
        $this->data = null;
        $this->process();
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

    private function process()
    {
        $parsed = ResponseParser::parse($this->raw_data, $this->format);

        if ($this->validate($parsed))
            $this->data = ResponseParser::clean($this->module_name, $parsed);
        else // No error, but no data retrieved
            $this->data = null;
    }

    private function validate($data)
    {
        if (isset($data['response']['error'])) {
            ApiErrorHandler::handle($data['response']['error']);
        }

        if (isset($data['response']['nodata'])) {
            // It is not a fatal error, so we won't raise an exception
            return false;
        }

        return true;
    }
}

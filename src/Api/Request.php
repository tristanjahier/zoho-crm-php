<?php

namespace Zoho\CRM\Api;

use Zoho\CRM\Connection;

class Request
{
    private $emitter;

    private $http_verb;

    private $format;

    private $module;

    private $method;

    private $parameters;

    public function __construct(Connection $emitter, $format, $module, $method, UrlParameters $parameters, $http_verb = HttpVerb::GET)
    {
        $this->emitter = $emitter;
        $this->http_verb = $http_verb;
        $this->format = $format;
        $this->module = $module;
        $this->method = $method;
        $this->parameters = $parameters;
    }

    public function getEmitter()
    {
        return $this->emitter;
    }

    public function getHttpVerb()
    {
        return $this->http_verb;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setFormat($value)
    {
        $this->format = $value;
    }

    public function setModule($value)
    {
        $this->module = $value;
    }

    public function setMethod($value)
    {
        $this->method = $value;
    }

    public function setParameters(UrlParameters $value)
    {
        $this->parameters = $value;
    }

    public function buildUri()
    {
        return $this->format . '/' .
               $this->module . '/' .
               $this->method . '?' .
               $this->parameters;
    }

    public function getModuleClass()
    {
        return $this->emitter->moduleClass($this->module);
    }
}

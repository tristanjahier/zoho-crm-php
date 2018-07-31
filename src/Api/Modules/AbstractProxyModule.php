<?php

namespace Zoho\Crm\Api\Modules;

abstract class AbstractProxyModule extends AbstractModule
{
    protected $mandator;

    public function __construct($owner, $module)
    {
        parent::__construct($owner);
        $this->mandator = $module;
    }

    public function mandatorModule()
    {
        return $this->mandator;
    }

    public function newQuery($method = null, $params = [], $paginated = false)
    {
        return parent::newQuery($method, $params, $paginated)->module($this->mandator);
    }
}

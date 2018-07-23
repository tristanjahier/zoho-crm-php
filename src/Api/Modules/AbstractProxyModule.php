<?php

namespace Zoho\Crm\Api\Modules;

abstract class AbstractProxyModule extends AbstractModule
{
    protected $mandated_module;

    public function __construct($owner, $module)
    {
        $this->mandated_module = $module;
        parent::__construct($owner);
    }

    public function mandatedModule()
    {
        return $this->mandated_module;
    }
}

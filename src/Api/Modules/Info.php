<?php

namespace Zoho\Crm\Api\Modules;

class Info extends AbstractModule
{
    protected static $supported_methods = [
        'getModules'
    ];

    public function getModules()
    {
        return $this->newQuery('getModules')->get();
    }
}

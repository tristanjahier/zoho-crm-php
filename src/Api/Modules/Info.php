<?php

namespace Zoho\Crm\Api\Modules;

class Info extends AbstractModule
{
    protected static $supported_methods = [
        'getModules'
    ];

    public function modules()
    {
        return $this->newQuery('getModules');
    }
}

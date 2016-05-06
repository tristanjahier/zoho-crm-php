<?php

namespace Zoho\CRM\Api\Modules;

class Info extends AbstractModule
{
    protected static $supported_methods = [
        'getModules'
    ];

    public function getModules()
    {
        return $this->request('getModules');
    }
}

<?php

namespace Zoho\CRM\Modules;

class Info extends AbstractModule
{
    protected $supported_methods = [
        'getModules'
    ];

    public function getModules()
    {
        return $this->request('getModules');
    }
}

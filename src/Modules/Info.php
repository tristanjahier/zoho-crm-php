<?php

namespace Zoho\CRM\Modules;

class Info extends AbstractModule
{
    public function __construct($owner)
    {
        parent::__construct($owner);
    }

    public function getModules()
    {
        return $this->request('getModules');
    }
}

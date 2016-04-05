<?php

namespace Zoho\CRM\Modules;

class Info extends AbstractModule
{
    public function getModules()
    {
        return $this->request('getModules');
    }
}

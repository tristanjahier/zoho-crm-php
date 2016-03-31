<?php

namespace Zoho\CRM\Modules;

use Zoho\CRM\Client as ZohoClient;

abstract class AbstractModule
{
    protected $supported_methods;

    protected $owner;

    private $module_name;

    function __construct(ZohoClient $owner)
    {
        $this->owner = $owner;
        $this->module_name = (new \ReflectionClass(get_class($this)))->getShortName();
    }

    public function getModuleName()
    {
        return $this->module_name;
    }

    public function getModuleOwner()
    {
        return $this->owner;
    }

    public function getSupportedMethods()
    {
        return $this->supported_methods;
    }

    protected function request($method, array $params = [])
    {
        return $this->owner->request($this->module_name, $method, $params);
    }
}

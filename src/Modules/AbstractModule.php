<?php

namespace Zoho\CRM\Modules;

use Zoho\CRM\Client as ZohoClient;

abstract class AbstractModule
{
    protected $supported_methods;

    private $owner;

    private $name;

    function __construct(ZohoClient $owner)
    {
        $this->owner = $owner;
        $this->name = (new \ReflectionClass(get_class($this)))->getShortName();
    }

    public function getModuleName()
    {
        return $this->name;
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
        return $this->owner->request($this->name, $method, $params)->getData();
    }
}

<?php

namespace Zoho\CRM\Modules;

use Zoho\CRM\Client as ZohoClient;

abstract class AbstractModule
{
    protected $supported_methods;

    private $owner;

    private $name;

    public function __construct(ZohoClient $owner)
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

    public function getFields(array $params = [], callable $filter = null)
    {
        $sections = $this->request('getFields', $params);

        if (isset($filter)) {
            foreach($sections as &$section) {
                $section['FL'] = array_filter($section['FL'], $filter);
                if (empty($section['FL']))
                    unset($section['FL']);
            }
        }

        return $sections;
    }

    public function getNativeFields()
    {
        return $this->getFields([], function($field) {
            return $field['customfield'] === 'false';
        });
    }

    public function getCustomFields()
    {
        return $this->getFields([], function($field) {
            return $field['customfield'] === 'true';
        });
    }

    public function getSummaryFields()
    {
        return $this->getFields(['type' => 1]);
    }

    public function getMandatoryFields()
    {
        return $this->getFields(['type' => 2]);
    }
}

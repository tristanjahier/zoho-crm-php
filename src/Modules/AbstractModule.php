<?php

namespace Zoho\CRM\Modules;

use Zoho\CRM\Client as ZohoClient;
use Zoho\CRM\Core\BaseClassStaticHelper;
use Doctrine\Common\Inflector\Inflector;

abstract class AbstractModule extends BaseClassStaticHelper
{
    protected static $name;

    protected static $associated_entity;

    protected $supported_methods = [];

    private $owner;

    public function __construct(ZohoClient $owner)
    {
        $this->owner = $owner;
    }

    public static function getModuleName()
    {
        return self::getChildStaticProperty('name', self::class, function() {
            return (new \ReflectionClass(static::class))->getShortName();
        });
    }

    public static function getAssociatedEntity()
    {
        return self::getChildStaticProperty('associated_entity', self::class, function() {
            return Inflector::singularize(self::getModuleName());
        });
    }

    public function getModuleOwner()
    {
        return $this->owner;
    }

    public function getSupportedMethods()
    {
        return $this->supported_methods;
    }

    public function supports($method)
    {
        return in_array($method, $this->supported_methods);
    }

    protected function request($method, array $params = [], $pagination = false)
    {
        return $this->owner->request(self::getModuleName(), $method, $params, $pagination);
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

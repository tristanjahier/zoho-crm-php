<?php

namespace Zoho\CRM\Api\Modules;

use Zoho\CRM\Client as ZohoClient;
use Zoho\CRM\BaseClassStaticHelper;
use Zoho\CRM\Api\UrlParameters;
use Zoho\CRM\Api\Modules\ModuleFields;
use Doctrine\Common\Inflector\Inflector;

abstract class AbstractModule extends BaseClassStaticHelper
{
    protected static $name;

    protected static $associated_entity;

    protected static $supported_methods = [];

    private $client;

    private $fields;

    protected $parameters_accumulator;

    public function __construct(ZohoClient $client)
    {
        $this->client = $client;
        $this->parameters_accumulator = new UrlParameters();

        // Add a meta module to retrieve this module's fields
        if (!($this instanceof ModuleFields)) {
            $this->fields = new ModuleFields($client, self::moduleName());
        }
    }

    public static function moduleName()
    {
        return self::getChildStaticProperty('name', function() {
            return (new \ReflectionClass(static::class))->getShortName();
        });
    }

    public static function associatedEntity()
    {
        return self::getChildStaticProperty('associated_entity', function() {
            return Inflector::singularize(self::moduleName());
        });
    }

    public static function supportedMethods()
    {
        return static::$supported_methods;
    }

    public static function supports($method)
    {
        return in_array($method, static::$supported_methods);
    }

    public function attachedClient()
    {
        return $this->client;
    }

    public function fields()
    {
        return $this->fields;
    }

    private function managedModule()
    {
        return $this instanceof AbstractProxyModule ? $this->mandatedModule() : self::moduleName();
    }

    protected function request($method, array $params = [], $pagination = false)
    {
        $params = $this->parameters_accumulator->extend($params)->toArray();
        $this->parameters_accumulator->reset();
        return $this->client->request($this->managedModule(), $method, $params, $pagination);
    }

    public function orderBy($column, $order = 'asc')
    {
        $this->parameters_accumulator['sortColumnString'] = $column;
        $this->parameters_accumulator['sortOrderString'] = $order;
        return $this;
    }

    public function modifiedAfter($date)
    {
        if (!($date instanceof \DateTime))
            $date = new \DateTime($date);

        $this->parameters_accumulator['lastModifiedTime'] = $date->format('Y-m-d H:i:s');
        return $this;
    }

    public function selectColumns(array $columns)
    {
        $selection_str = $this->managedModule() . '(' . implode(',', $columns) . ')';
        $this->parameters_accumulator['selectColumns'] = $selection_str;
        return $this;
    }
}

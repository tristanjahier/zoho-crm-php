<?php

namespace Zoho\Crm\Api\Modules;

use BadMethodCallException;
use InvalidArgumentException;
use Zoho\Crm\Client;
use Zoho\Crm\Support\ClassShortNameTrait;
use Zoho\Crm\Api\Modules\ModuleFields;
use Doctrine\Common\Inflector\Inflector;

abstract class AbstractModule
{
    use ClassShortNameTrait;

    protected static $name;

    protected static $associated_entity;

    protected static $supported_methods = [];

    private $client;

    private $fields;

    public function __construct(Client $client)
    {
        $this->client = $client;

        // Add a meta module to retrieve this module's fields
        if ($this->supports('getFields') && ! ($this instanceof ModuleFields)) {
            $this->fields = new ModuleFields($client, self::name());
        }
    }

    public static function name()
    {
        return isset(static::$name) ? static::$name : self::getClassShortName();
    }

    public static function associatedEntity()
    {
        return static::$associated_entity;
    }

    public static function hasAssociatedEntity()
    {
        return static::$associated_entity !== null;
    }

    public static function supportedMethods()
    {
        return static::$supported_methods;
    }

    public static function supports($method)
    {
        return in_array($method, static::$supported_methods);
    }

    public function client()
    {
        return $this->client;
    }

    public function fields()
    {
        return $this->fields;
    }

    public function newQuery($method = null, $params = [], $paginated = false)
    {
        return $this->client->newQuery(self::name(), $method, $params, $paginated);
    }

    public function newEntity($properties = [])
    {
        $class = static::$associated_entity;

        return new $class($properties);
    }

    public function __call($method, $arguments)
    {
        $className = static::class;

        if ($this->supports($method)) {
            $query = $this->newQuery($method);

            if (count($arguments) > 0) {
                $query->params($arguments[0]);
            }

            if (count($arguments) > 1) {
                throw new InvalidArgumentException("Method {$className}::{$method}() takes only 1 optional argument.");
            }

            return $query;
        }

        throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }
}

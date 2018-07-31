<?php

namespace Zoho\Crm\Api\Modules;

use Zoho\Crm\Connection;
use Zoho\Crm\ClassShortNameTrait;
use Zoho\Crm\Api\Modules\ModuleFields;
use Doctrine\Common\Inflector\Inflector;

abstract class AbstractModule
{
    use ClassShortNameTrait;

    protected static $name;

    protected static $associated_entity;

    protected static $supported_methods = [];

    private $connection;

    private $fields;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        // Add a meta module to retrieve this module's fields
        if ($this->supports('getFields') && ! ($this instanceof ModuleFields)) {
            $this->fields = new ModuleFields($connection, self::name());
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

    public function connection()
    {
        return $this->connection;
    }

    public function fields()
    {
        return $this->fields;
    }

    public function newQuery($method = null, $params = [], $paginated = false)
    {
        return $this->connection->newQuery(self::name(), $method, $params, $paginated);
    }
}

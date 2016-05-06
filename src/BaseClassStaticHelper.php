<?php

namespace Zoho\CRM;

abstract class BaseClassStaticHelper
{
    protected static function createChildStaticProperty($class, $property_name)
    {
        $tmp = null;
        $class::$$property_name =& $tmp;
        unset($tmp);
    }

    protected static function getChildStaticProperty($property_name, callable $default)
    {
        if (isset(static::$$property_name) || static::hasOwnProperty($property_name)) {
            return static::$$property_name;
        } elseif (!static::isAbstract()) {
            self::createChildStaticProperty(static::class, $property_name);
            return static::$$property_name = $default();
        } else {
            return null;
        }
    }

    protected static function hasOwnProperty($property_name)
    {
        $property = (new \ReflectionClass(static::class))->getProperty($property_name);
        return $property->class === static::class;
    }

    protected static function isAbstract()
    {
        return (new \ReflectionClass(static::class))->isAbstract();
    }
}

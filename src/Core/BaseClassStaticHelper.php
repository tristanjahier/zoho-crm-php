<?php

namespace Zoho\CRM\Core;

abstract class BaseClassStaticHelper
{
    protected static function createChildStaticProperty($class, $property_name)
    {
        $tmp = null;
        $class::$$property_name =& $tmp;
        unset($tmp);
    }

    protected static function getChildStaticProperty($property_name, $base_class, callable $default)
    {
        if (isset(static::$$property_name)) {
            return static::$$property_name;
        } elseif (static::class !== $base_class) {
            self::createChildStaticProperty(static::class, $property_name);
            return static::$$property_name = $default();
        } else {
            return $default();
        }
    }
}

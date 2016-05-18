<?php

namespace Zoho\CRM\Entities;

use Zoho\CRM\BaseClassStaticHelper;
use Zoho\CRM\Exception\UnsupportedEntityPropertyException;

abstract class AbstractEntity extends BaseClassStaticHelper
{
    protected static $name;

    protected static $property_aliases = [];

    protected $properties = [];

    public function __construct(array $data = [])
    {
        $this->properties = $data;
    }

    public static function entityName()
    {
        return self::getChildStaticProperty('name', function() {
            return (new \ReflectionClass(static::class))->getShortName();
        });
    }

    public static function supportedProperties()
    {
        return array_values(static::$property_aliases);
    }

    public static function supports($property)
    {
        return in_array($property, static::supportedProperties());
    }

    public function has($property)
    {
        $clean = array_key_exists($property, static::$property_aliases) &&
                 isset($this->properties[static::$property_aliases[$property]]);
        $raw   = isset($this->properties[$property]);
        return $clean || $raw;
    }

    public function get($property)
    {
        // Permissive mode: allows raw and clean property names
        if (array_key_exists($property, static::$property_aliases)) {
            $property = static::$property_aliases[$property];
        }

        return isset($this->properties[$property]) ? $this->properties[$property] : null;
    }

    public function set($property, $value)
    {
        // Permissive mode: allows raw and clean property names
        if (array_key_exists($property, static::$property_aliases)) {
            $property = static::$property_aliases[$property];
        }

        $this->properties[$property] = $value;
    }

    public function rawData()
    {
        return $this->properties;
    }

    public function toArray()
    {
        $hash = [];

        // Reverse the properties keys mapping,
        // from ['clean_name' => 'ZOHO NAME'] to ['ZOHO NAME' => 'clean_name']
        $reversed_property_aliases = array_flip(static::$property_aliases);

        // Generate a new hashmap with the entity's properties names as keys
        foreach ($this->properties as $key => $value) {
            if (array_key_exists($key, $reversed_property_aliases)) {
                $hash[$reversed_property_aliases[$key]] = $value;
            }
        }

        return $hash;
    }

    public function __get($alias)
    {
        if (array_key_exists($alias, static::$property_aliases)) {
            if (isset($this->properties[static::$property_aliases[$alias]])) {
                return $this->properties[static::$property_aliases[$alias]];
            } else {
                return null;
            }
        } else {
            throw new UnsupportedEntityPropertyException($this->entityName(), $alias);
        }
    }

    public function __set($alias, $value)
    {
        if (array_key_exists($alias, static::$property_aliases)) {
            $this->properties[static::$property_aliases[$alias]] = $value;
        } else {
            throw new UnsupportedEntityPropertyException($this->entityName(), $alias);
        }
    }

    public function __toString()
    {
        return print_r($this->toArray(), true);
    }
}

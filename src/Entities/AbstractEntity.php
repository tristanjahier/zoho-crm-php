<?php

namespace Zoho\CRM\Entities;

use Zoho\CRM\BaseClassStaticHelper;
use Zoho\CRM\Exception\UnsupportedEntityPropertyException;

abstract class AbstractEntity extends BaseClassStaticHelper
{
    protected static $name;

    protected static $properties_mapping = [];

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
        return array_values(static::$properties_mapping);
    }

    public static function supports($property)
    {
        return in_array($property, static::supportedProperties());
    }

    public function has($property)
    {
        $clean = array_key_exists($property, static::$properties_mapping) &&
                 isset($this->properties[static::$properties_mapping[$property]]);
        $raw   = isset($this->properties[$property]);
        return $clean || $raw;
    }

    public function get($property)
    {
        // Permissive mode: allows raw and clean property names
        if (array_key_exists($property, static::$properties_mapping)) {
            $property = static::$properties_mapping[$property];
        }

        return isset($this->properties[$property]) ? $this->properties[$property] : null;
    }

    public function set($property, $value)
    {
        // Permissive mode: allows raw and clean property names
        if (array_key_exists($property, static::$properties_mapping)) {
            $property = static::$properties_mapping[$property];
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
        $reversed_properties_mapping = array_flip(static::$properties_mapping);

        // Generate a new hashmap with the entity's properties names as keys
        foreach ($this->properties as $key => $value) {
            if (array_key_exists($key, $reversed_properties_mapping)) {
                $hash[$reversed_properties_mapping[$key]] = $value;
            }
        }

        return $hash;
    }

    public function __get($property)
    {
        if (array_key_exists($property, static::$properties_mapping)) {
            if (isset($this->properties[static::$properties_mapping[$property]])) {
                return $this->properties[static::$properties_mapping[$property]];
            } else {
                return null;
            }
        } else {
            throw new UnsupportedEntityPropertyException($this->entityName(), $property);
        }
    }

    public function __set($property, $newvalue)
    {
        if (array_key_exists($property, static::$properties_mapping)) {
            $this->properties[static::$properties_mapping[$property]] = $newvalue;
        } else {
            throw new UnsupportedEntityPropertyException($this->entityName(), $property);
        }
    }

    public function __toString()
    {
        return print_r($this->toArray(), true);
    }
}

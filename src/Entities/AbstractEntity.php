<?php

namespace Zoho\CRM\Entities;

use Zoho\CRM\ClassShortNameTrait;
use Zoho\CRM\Exception\UnsupportedEntityPropertyException;
use Zoho\CRM\Api\Response;

abstract class AbstractEntity
{
    use ClassShortNameTrait;

    protected static $name;

    protected static $property_aliases = [];

    protected $properties = [];

    public function __construct(array $data = [])
    {
        $this->properties = $data;
    }

    public static function name()
    {
        return isset(static::$name) ? static::$name : self::getClassShortName();
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

        // Reverse the property aliases mapping,
        // from ['clean_name' => 'ZOHO NAME'] to ['ZOHO NAME' => 'clean_name']
        $reversed_property_aliases = array_flip(static::$property_aliases);

        // Generate a new hashmap with the entity's property aliases as keys
        foreach ($reversed_property_aliases as $prop => $alias) {
            if (array_key_exists($prop, $this->properties)) {
                $hash[$alias] = $this->properties[$prop];
            }
        }

        return $hash;
    }

    public function copy()
    {
        // Just a simple shallow copy because entities only have primitives attributes
        return clone $this;
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
            throw new UnsupportedEntityPropertyException($this->name(), $alias);
        }
    }

    public function __set($alias, $value)
    {
        if (array_key_exists($alias, static::$property_aliases)) {
            $this->properties[static::$property_aliases[$alias]] = $value;
        } else {
            throw new UnsupportedEntityPropertyException($this->name(), $alias);
        }
    }

    public function __toString()
    {
        return print_r($this->toArray(), true);
    }

    public static function createFromResponse(Response $response)
    {
        if ($response->getContent() === null) {
            return null;
        }

        $module_class = $response->getRequest()->getModuleClass();
        $entity_class = $module_class::associatedEntity();

        return new $entity_class($response->getContent());
    }
}

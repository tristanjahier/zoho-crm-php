<?php

namespace Zoho\Crm\Entities;

use Doctrine\Common\Inflector\Inflector;
use Zoho\Crm\Client;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\Support\ClassShortNameTrait;
use Zoho\Crm\Support\Arrayable;
use Zoho\Crm\Exceptions\UnsupportedEntityPropertyException;
use Zoho\Crm\Api\Response;

/**
 * Base class of entities.
 */
abstract class AbstractEntity implements Arrayable
{
    use ClassShortNameTrait;

    /** @var string|null The name of the entity */
    protected static $name;

    /** @var string|null The name of the related module */
    protected static $module_name;

    /** @var string[] An associative array of aliases pointing to real property names */
    protected static $property_aliases = [];

    /** @var \Zoho\Crm\Client|null The client to which the entity is bound */
    protected $client;

    /** @var string[] The entity properties (the data) */
    protected $properties = [];

    /**
     * The constructor.
     *
     * @param string[] $data (optional) The entity data
     * @param \Zoho\Crm\Client $client (optional) The client to which the entity must be bound
     */
    public function __construct(array $data = [], Client $client = null)
    {
        $this->properties = $this->unaliasProperties($data);
        $this->client = $client;
    }

    /**
     * Get the name of the entity.
     *
     * @return string
     */
    public static function name()
    {
        return isset(static::$name) ? static::$name : self::getClassShortName();
    }

    /**
     * Get the name of the related module.
     *
     * @return string
     */
    public static function moduleName()
    {
        if (isset(static::$module_name)) {
            return static::$module_name;
        }

        return Inflector::pluralize(static::name());
    }

    /**
     * Get the list of properties supported by default.
     *
     * @return string[]
     */
    public static function supportedProperties()
    {
        return array_values(static::$property_aliases);
    }

    /**
     * Check if a property is supported by default.
     *
     * @param string $property The name of the property
     * @return bool
     */
    public static function supports($property)
    {
        return in_array($property, static::supportedProperties());
    }

    /**
     * Check if a property is defined.
     *
     * @param string $property The name of the property
     * @return bool
     */
    public function has($property)
    {
        $clean = array_key_exists($property, static::$property_aliases) &&
                 isset($this->properties[static::$property_aliases[$property]]);
        $raw   = isset($this->properties[$property]);
        return $clean || $raw;
    }

    /**
     * Get the value of a property.
     *
     * @param string $property The name of the property
     * @return string|null
     */
    public function get($property)
    {
        // Permissive mode: allows raw and clean property names
        if (array_key_exists($property, static::$property_aliases)) {
            $property = static::$property_aliases[$property];
        }

        return isset($this->properties[$property]) ? $this->properties[$property] : null;
    }

    /**
     * Set the value of a property.
     *
     * @param string $property The name of the property
     * @param string $value The value of the property
     * @return void
     */
    public function set($property, $value)
    {
        // Permissive mode: allows raw and clean property names
        if (array_key_exists($property, static::$property_aliases)) {
            $property = static::$property_aliases[$property];
        }

        $this->properties[$property] = $value;
    }

    /**
     * Check if an alias exists for a given property.
     *
     * @param string $property The name of the property
     * @return bool
     */
    public function hasAlias($property)
    {
        return in_array($property, static::$property_aliases);
    }

    /**
     * Check if a given alias exists.
     *
     * @param string $alias The alias name
     * @return bool
     */
    public function isAlias($alias)
    {
        return array_key_exists($alias, static::$property_aliases);
    }

    /**
     * Get the actual name of a property behind a given alias.
     *
     * @param string $alias The alias name
     * @return string
     */
    public function unalias($alias)
    {
        return static::$property_aliases[$alias];
    }

    /**
     * Unalias the keys of a given properties array.
     *
     * @param string[] $properties The properties array
     * @return string[]
     */
    private function unaliasProperties(array $properties)
    {
        $unaliased_keys = array_map(function ($prop) {
            return $this->isAlias($prop) ? $this->unalias($prop) : $prop;
        }, array_keys($properties));

        return array_combine($unaliased_keys, $properties);
    }

    /**
     * Get the value of the primary key / identifier property.
     *
     * @return string
     */
    public function key()
    {
        return $this->get($this->module()->primaryKey());
    }

    /**
     * Get the raw properties array.
     *
     * @return string[]
     */
    public function toArray()
    {
        return $this->properties;
    }

    /**
     * Get an aliased properties array.
     *
     * If an existing property has no alias then it will not be present in the array.
     *
     * @return string[]
     */
    public function toAliasArray()
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

    /**
     * Get the client to which the entity is bound.
     *
     * @return \Zoho\Crm\Client|null
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the client to which the entity is bound.
     *
     * @param \Zoho\Crm\Client|null $client The client to which the entity must be bound
     * @return void
     */
    public function setClient(?Client $client)
    {
        $this->client = $client;
    }

    /**
     * Check if the entity is bound to a client.
     *
     * @return bool
     */
    public function isDetached()
    {
        return is_null($this->client);
    }

    /**
     * Get the related module handler.
     *
     * @return \Zoho\Crm\Api\Modules\AbstractModule|null
     */
    public function module()
    {
        if ($this->isDetached()) {
            return null;
        }

        return $this->client->module(static::moduleName());
    }

    /**
     * Copy the entity object.
     *
     * @return static
     */
    public function copy()
    {
        // Just a simple shallow copy because entities only have primitives attributes
        return clone $this;
    }

    /**
     * Dynamically retrieve a property by its alias.
     *
     * @param string $alias The property alias name
     * @return string|null
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedEntityPropertyException
     */
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

    /**
     * Dynamically set a property value by its alias.
     *
     * @param string $alias The property alias name
     * @param string $value The value of the property
     * @return void
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedEntityPropertyException
     */
    public function __set($alias, $value)
    {
        if (array_key_exists($alias, static::$property_aliases)) {
            $this->properties[static::$property_aliases[$alias]] = $value;
        } else {
            throw new UnsupportedEntityPropertyException($this->name(), $alias);
        }
    }

    /**
     * Determine if a property is present by its alias.
     *
     * @param string $alias The property alias name
     * @return bool
     */
    public function __isset($alias)
    {
        return $this->isAlias($alias)
            && array_key_exists($this->unalias($alias), $this->properties);
    }

    /**
     * Return a string representation of the entity.
     *
     * @return string
     */
    public function __toString()
    {
        return print_r($this->toArray(), true);
    }

    /**
     * Prepare for serialization and return the object properties to serialize.
     *
     * @return string[]
     */
    public function __sleep()
    {
        // $properties is the only member that need to be serialized
        return ['properties'];
    }
}

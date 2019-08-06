<?php

namespace Zoho\Crm\Entities;

use Doctrine\Common\Inflector\Inflector;
use Zoho\Crm\Client;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\Support\ClassShortNameTrait;
use Zoho\Crm\Support\Arrayable;
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
    protected static $moduleName;

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
        $this->properties = $data;
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
        if (isset(static::$moduleName)) {
            return static::$moduleName;
        }

        return Inflector::pluralize(static::name());
    }

    /**
     * Check if a property is defined.
     *
     * @param string $property The name of the property
     * @return bool
     */
    public function has($property)
    {
        return isset($this->properties[$property]);
    }

    /**
     * Get the value of a property.
     *
     * @param string $property The name of the property
     * @return string|null
     */
    public function get($property)
    {
        return $this->properties[$property] ?? null;
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
        $this->properties[$property] = $value;
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
     * Dynamically retrieve a property as if it was a public property.
     *
     * @param string $property The name of the property
     * @return string|null
     */
    public function __get($property)
    {
        return $this->get($property);
    }

    /**
     * Dynamically set a property value as if it was a public property.
     *
     * @param string $property The name of the property
     * @param string $value The value of the property
     * @return void
     */
    public function __set($property, $value)
    {
        $this->set($property, $value);
    }

    /**
     * Determine if a property is present as if it was a public property.
     *
     * @param string $property The name of the property
     * @return bool
     */
    public function __isset($property)
    {
        return $this->has($property);
    }

    /**
     * Return a string representation of the entity.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode([
            'type' => static::name(),
            'properties' => $this->toArray(),
        ], JSON_PRETTY_PRINT);
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

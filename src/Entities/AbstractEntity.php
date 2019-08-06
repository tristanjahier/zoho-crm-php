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

    /** @var string[] The entity attributes */
    protected $attributes = [];

    /** @var \Zoho\Crm\Client|null The client to which the entity is bound */
    protected $client;

    /**
     * The constructor.
     *
     * @param string[] $attributes (optional) The entity attributes
     * @param \Zoho\Crm\Client $client (optional) The client to which the entity must be bound
     */
    public function __construct(array $attributes = [], Client $client = null)
    {
        $this->attributes = $attributes;
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
     * Check if an attribute is defined.
     *
     * @param string $attribute The name of the attribute
     * @return bool
     */
    public function has($attribute)
    {
        return isset($this->attributes[$attribute]);
    }

    /**
     * Get the value of an attribute.
     *
     * @param string $attribute The name of the attribute
     * @return string|null
     */
    public function get($attribute)
    {
        return $this->attributes[$attribute] ?? null;
    }

    /**
     * Set the value of an attribute.
     *
     * @param string $attribute The name of the attribute
     * @param string $value The value of the attribute
     * @return void
     */
    public function set($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }

    /**
     * Get the value of the primary key / identifier attribute.
     *
     * @return string
     */
    public function key()
    {
        return $this->get($this->module()->primaryKey());
    }

    /**
     * Get the raw attributes array.
     *
     * @return string[]
     */
    public function toArray()
    {
        return $this->attributes;
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
     * Get the value of an attribute as if it was a public property.
     *
     * @param string $attribute The name of the attribute
     * @return string|null
     */
    public function __get($attribute)
    {
        return $this->get($attribute);
    }

    /**
     * Set the value of an attribute as if it was a public property.
     *
     * @param string $attribute The name of the attribute
     * @param string $value The value of the attribute
     * @return void
     */
    public function __set($attribute, $value)
    {
        $this->set($attribute, $value);
    }

    /**
     * Check if an attribute is defined as if it was a public property.
     *
     * @param string $attribute The name of the attribute
     * @return bool
     */
    public function __isset($attribute)
    {
        return $this->has($attribute);
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
            'attributes' => $this->toArray(),
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Prepare for serialization and return the object properties to serialize.
     *
     * @return string[]
     */
    public function __sleep()
    {
        // $attributes is the only property that needs to be serialized
        return ['attributes'];
    }
}

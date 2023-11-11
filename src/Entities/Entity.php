<?php

declare(strict_types=1);

namespace Zoho\Crm\Entities;

use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Support\Arrayable;
use Zoho\Crm\Support\Helper;

/**
 * Default minimal implementation of an API entity.
 */
class Entity implements Arrayable
{
    /** @var string|null The name of the entity */
    protected static $name;

    /** @var string|null The name of the identifier attribute */
    protected static $idName;

    /** @var string[] The entity attributes */
    protected $attributes = [];

    /** @var \Zoho\Crm\Contracts\ClientInterface|null The client to which the entity is bound */
    protected $client;

    /**
     * The constructor.
     *
     * @param string[] $attributes (optional) The entity attributes
     * @param \Zoho\Crm\Contracts\ClientInterface|null $client (optional) The client to which the entity is bound
     */
    public function __construct(array $attributes = [], ClientInterface $client = null)
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
        return isset(static::$name) ? static::$name : Helper::getClassShortName(static::class);
    }

    /**
     * Get the name of the identifier attribute.
     *
     * @return string
     */
    public static function idName()
    {
        return static::$idName;
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
     * Remove an attribute.
     *
     * The attribute will be completely unset, not just set to null.
     *
     * @param string $attribute The name of the attribute
     * @return void
     */
    public function unset($attribute)
    {
        unset($this->attributes[$attribute]);
    }

    /**
     * Get the entity ID.
     *
     * @return string|null
     */
    public function getId()
    {
        if (is_null($idName = static::idName())) {
            return null;
        }

        return $this->get($idName);
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
     * @return \Zoho\Crm\Contracts\ClientInterface|null
     */
    public function getClient(): ?ClientInterface
    {
        return $this->client;
    }

    /**
     * Set the client to which the entity is bound.
     *
     * @param \Zoho\Crm\Contracts\ClientInterface|null $client The client to which the entity is bound
     * @return void
     */
    public function setClient(?ClientInterface $client)
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
     * Unset an attribute as if it was a public property.
     *
     * @param string $attribute The name of the attribute
     * @return void
     */
    public function __unset($attribute)
    {
        $this->unset($attribute);
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

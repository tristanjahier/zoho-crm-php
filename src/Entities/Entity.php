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
    /** The name of the entity */
    protected static ?string $name;

    /** The name of the identifier attribute */
    protected static ?string $idName;

    /**
     * The entity attributes.
     *
     * @var string[]
     */
    protected array $attributes = [];

    /** The client to which the entity is bound */
    protected ?ClientInterface $client;

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
     */
    public static function name(): string
    {
        return isset(static::$name) ? static::$name : Helper::getClassShortName(static::class);
    }

    /**
     * Get the name of the identifier attribute.
     */
    public static function idName(): string
    {
        return static::$idName;
    }

    /**
     * Check if an attribute is defined.
     *
     * @param string $attribute The name of the attribute
     */
    public function has(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
    }

    /**
     * Get the value of an attribute.
     *
     * @param string $attribute The name of the attribute
     */
    public function get(string $attribute): mixed
    {
        return $this->attributes[$attribute] ?? null;
    }

    /**
     * Set the value of an attribute.
     *
     * @param string $attribute The name of the attribute
     * @param mixed $value The value of the attribute
     */
    public function set(string $attribute, mixed $value): void
    {
        $this->attributes[$attribute] = $value;
    }

    /**
     * Remove an attribute.
     *
     * The attribute will be completely unset, not just set to null.
     *
     * @param string $attribute The name of the attribute
     */
    public function unset(string $attribute): void
    {
        unset($this->attributes[$attribute]);
    }

    /**
     * Get the entity ID.
     */
    public function getId(): ?string
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
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Get the client to which the entity is bound.
     */
    public function getClient(): ?ClientInterface
    {
        return $this->client;
    }

    /**
     * Set the client to which the entity is bound.
     *
     * @param \Zoho\Crm\Contracts\ClientInterface|null $client The client to which the entity is bound
     */
    public function setClient(?ClientInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * Check if the entity is bound to a client.
     */
    public function isDetached(): bool
    {
        return is_null($this->client);
    }

    /**
     * Copy the entity object.
     */
    public function copy(): static
    {
        // Just a simple shallow copy because entities only have primitive properties
        return clone $this;
    }

    /**
     * Get the value of an attribute as if it was a public property.
     *
     * @param string $attribute The name of the attribute
     */
    public function __get(string $attribute): mixed
    {
        return $this->get($attribute);
    }

    /**
     * Set the value of an attribute as if it was a public property.
     *
     * @param string $attribute The name of the attribute
     * @param mixed $value The value of the attribute
     */
    public function __set(string $attribute, mixed $value): void
    {
        $this->set($attribute, $value);
    }

    /**
     * Check if an attribute is defined as if it was a public property.
     *
     * @param string $attribute The name of the attribute
     */
    public function __isset(string $attribute): bool
    {
        return $this->has($attribute);
    }

    /**
     * Unset an attribute as if it was a public property.
     *
     * @param string $attribute The name of the attribute
     */
    public function __unset(string $attribute): void
    {
        $this->unset($attribute);
    }

    /**
     * Return a string representation of the entity.
     */
    public function __toString(): string
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
    public function __sleep(): array
    {
        // $attributes is the only property that needs to be serialized
        return ['attributes'];
    }
}

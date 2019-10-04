<?php

namespace Zoho\Crm\Api\Modules;

use BadMethodCallException;
use InvalidArgumentException;
use Zoho\Crm\Client;
use Zoho\Crm\Support\ClassShortNameTrait;
use Zoho\Crm\Api\Modules\ModuleFields;
use Zoho\Crm\Entities\Entity;
use Doctrine\Common\Inflector\Inflector;

/**
 * Base class of the API module handlers.
 */
abstract class AbstractModule
{
    use ClassShortNameTrait;

    /** @var string The name of the API module */
    protected static $name;

    /** @var string The associated entity class name */
    protected static $associatedEntity = Entity::class;

    /** @var string[] The list of API methods supported by this module */
    protected static $supportedMethods = [];

    /** @var \Zoho\Crm\Client The client to which the module is attached */
    private $client;

    /** @var ModuleFields|null The metamodule to inspect the module's fields */
    private $fields;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Client $client The client to which the module is attached
     */
    public function __construct(Client $client)
    {
        $this->client = $client;

        // Add a metamodule to retrieve this module's fields
        if ($this->supports('getFields') && ! ($this instanceof ModuleFields)) {
            $this->fields = new ModuleFields($client, self::name());
        }
    }

    /**
     * Get the name of the API module handled by this class.
     *
     * @return string
     */
    public static function name()
    {
        return isset(static::$name) ? static::$name : self::getClassShortName();
    }

    /**
     * Get the name of the entity class associated to the module.
     *
     * @return string
     */
    public static function associatedEntity()
    {
        return static::$associatedEntity;
    }

    /**
     * Check if there is an entity associated to the module.
     *
     * @return bool
     */
    public static function hasAssociatedEntity()
    {
        return static::$associatedEntity !== null;
    }

    /**
     * Get the list of the supported API methods.
     *
     * @return string[]
     */
    public static function supportedMethods()
    {
        return static::$supportedMethods;
    }

    /**
     * Check if a method is supported by the module.
     *
     * @param string $method The name of the method
     * @return bool
     */
    public static function supports($method)
    {
        return in_array($method, static::$supportedMethods);
    }

    /**
     * Get the client to which the module is attached.
     *
     * @return \Zoho\Crm\Client
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Get the metamodule to inspect the module's fields.
     *
     * @return ModuleFields
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Create a new query for this module.
     *
     * @param string|null $method (optional) The name of the API method
     * @param array $params (optional) An array of URL parameters
     * @param bool $paginated (optional) Whether to fetch multiple pages or not
     * @return \Zoho\Crm\Api\Query
     */
    public function newQuery($method = null, $params = [], $paginated = false)
    {
        return $this->client->newQuery(self::name(), $method, $params, $paginated);
    }

    /**
     * Create a new entity.
     *
     * @param array $attributes (optional) The entity attributes
     * @return \Zoho\Crm\Entities\Entity
     */
    public function newEntity($attributes = [])
    {
        $class = static::$associatedEntity;

        return new $class($attributes, $this->client);
    }

    /**
     * Create a new query by dynamically calling the name of a supported method.
     *
     * @param string $method The name of the API method
     * @param array $arguments The method arguments
     * @return \Zoho\Crm\Api\Query
     *
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     */
    public function __call($method, $arguments)
    {
        $className = static::class;

        if ($this->supports($method)) {
            $query = $this->newQuery($method);

            if (count($arguments) > 0) {
                $query->params($arguments[0]);
            }

            if (count($arguments) > 1) {
                throw new InvalidArgumentException("Method {$className}::{$method}() takes only 1 optional argument.");
            }

            return $query;
        }

        throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }
}

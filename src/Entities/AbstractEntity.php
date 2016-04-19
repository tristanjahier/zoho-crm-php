<?php

namespace Zoho\CRM\Entities;

use Zoho\CRM\Core\BaseClassStaticHelper;
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

    public static function getEntityName()
    {
        return self::getChildStaticProperty('name', self::class, function() {
            return (new \ReflectionClass(static::class))->getShortName();
        });
    }

    public function toArray()
    {
        return $this->properties;
    }

    public function __get($property)
    {
        if (array_key_exists($property, static::$properties_mapping))
            return $this->properties[static::$properties_mapping[$property]];
        else
            throw new UnsupportedEntityPropertyException($this->getEntityName(), $property);
    }
}

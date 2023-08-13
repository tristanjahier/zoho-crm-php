<?php

namespace Zoho\Crm\V1\Entities;

use Zoho\Crm\Entities\Entity as BaseEntity;
use Zoho\Crm\Support\Helper;

/**
 * Default minimal implementation of an API v1 entity.
 */
class Entity extends BaseEntity
{
    /** @var string|null The name of the related module */
    protected static $moduleName;

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

        return Helper::inflector()->pluralize(static::name());
    }

    /**
     * @inheritdoc
     */
    public static function idName()
    {
        if (isset(static::$idName)) {
            return static::$idName;
        }

        return strtoupper(static::name()) . 'ID';
    }

    /**
     * Get the related module handler.
     *
     * @return \Zoho\Crm\V1\Modules\AbstractModule|null
     */
    public function module()
    {
        if ($this->isDetached()) {
            return null;
        }

        return $this->client->module(static::moduleName());
    }
}

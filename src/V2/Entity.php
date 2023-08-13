<?php

namespace Zoho\Crm\V2;

use Zoho\Crm\Entities\Entity as BaseEntity;

/**
 * Default minimal implementation of an API v2 entity.
 */
class Entity extends BaseEntity
{
    /**
     * @inheritdoc
     */
    public static function idName()
    {
        if (isset(static::$idName)) {
            return static::$idName;
        }

        return 'id';
    }
}

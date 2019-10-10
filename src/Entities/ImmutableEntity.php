<?php

namespace Zoho\Crm\Entities;

use Zoho\Crm\Exceptions\ImmutableEntityException;

/**
 * Implementation of an immutable API entity.
 */
class ImmutableEntity extends Entity
{
    /**
     * @inheritdoc
     *
     * @throws \Zoho\Crm\Exceptions\ImmutableEntityException
     */
    public function set($attribute, $value)
    {
        throw new ImmutableEntityException();
    }

    /**
     * @inheritdoc
     *
     * @throws \Zoho\Crm\Exceptions\ImmutableEntityException
     */
    public function __set($attribute, $value)
    {
        throw new ImmutableEntityException();
    }
}

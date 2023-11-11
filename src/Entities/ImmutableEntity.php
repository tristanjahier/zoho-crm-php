<?php

declare(strict_types=1);

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
    final public function set($attribute, $value)
    {
        throw new ImmutableEntityException();
    }

    /**
     * @inheritdoc
     *
     * @throws \Zoho\Crm\Exceptions\ImmutableEntityException
     */
    final public function __set($attribute, $value)
    {
        throw new ImmutableEntityException();
    }

    /**
     * @inheritdoc
     *
     * @throws \Zoho\Crm\Exceptions\ImmutableEntityException
     */
    final public function unset($attribute)
    {
        throw new ImmutableEntityException();
    }

    /**
     * @inheritdoc
     *
     * @throws \Zoho\Crm\Exceptions\ImmutableEntityException
     */
    final public function __unset($attribute)
    {
        throw new ImmutableEntityException();
    }
}

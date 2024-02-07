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
    final public function set(string $attribute, mixed $value): void
    {
        throw new ImmutableEntityException();
    }

    /**
     * @inheritdoc
     *
     * @throws \Zoho\Crm\Exceptions\ImmutableEntityException
     */
    final public function __set(string $attribute, mixed $value): void
    {
        throw new ImmutableEntityException();
    }

    /**
     * @inheritdoc
     *
     * @throws \Zoho\Crm\Exceptions\ImmutableEntityException
     */
    final public function unset(string $attribute): void
    {
        throw new ImmutableEntityException();
    }

    /**
     * @inheritdoc
     *
     * @throws \Zoho\Crm\Exceptions\ImmutableEntityException
     */
    final public function __unset(string $attribute): void
    {
        throw new ImmutableEntityException();
    }
}

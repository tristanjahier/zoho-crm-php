<?php

declare(strict_types=1);

namespace Zoho\Crm\Support;

/**
 * Interface that ensures that a class instance can be transformed into an array.
 */
interface Arrayable
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array;
}

<?php

namespace Zoho\Crm\V2\Middleware;

use Zoho\Crm\Contracts\MiddlewareInterface;
use Zoho\Crm\Contracts\QueryInterface;

/**
 * Middleware that validates queries.
 */
class Validation implements MiddlewareInterface
{
    /**
     * @inheritdoc
     *
     * @throws \Zoho\Crm\Exceptions\InvalidQueryException
     */
    public function __invoke(QueryInterface $query): void
    {
        // Additional internal validation logic
        $query->validate();
    }
}

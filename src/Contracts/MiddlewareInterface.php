<?php

namespace Zoho\Crm\Contracts;

interface MiddlewareInterface
{
    /**
     * Apply the middleware on the query.
     *
     * @param QueryInterface $query The query
     * @return void
     */
    public function __invoke(QueryInterface $query): void;
}

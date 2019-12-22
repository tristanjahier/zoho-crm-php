<?php

namespace Zoho\Crm\Contracts;

use Exception;

interface ErrorHandlerInterface
{
    /**
     * Handle an exception (related to an API error) thrown from the request sender.
     *
     * @param \Exception $exception The exception to handle
     * @param QueryInterface $query The query that failed
     * @return void
     *
     * @throws \Exception
     */
    public function handle(Exception $exception, QueryInterface $query): void;
}

<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

use Exception;

interface ErrorHandlerInterface
{
    /**
     * Handle an exception (related to an API error) thrown from the HTTP layer.
     *
     * @param \Exception $exception The exception to handle
     * @param RequestInterface $request The request that failed
     *
     * @throws \Exception
     */
    public function handle(Exception $exception, RequestInterface $request): void;
}

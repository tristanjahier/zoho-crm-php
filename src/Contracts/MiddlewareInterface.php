<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

interface MiddlewareInterface
{
    /**
     * Apply the middleware on the request.
     *
     * @param RequestInterface $request The request
     */
    public function __invoke(RequestInterface $request): void;
}

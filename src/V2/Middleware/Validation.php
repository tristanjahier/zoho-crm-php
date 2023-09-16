<?php

namespace Zoho\Crm\V2\Middleware;

use Zoho\Crm\Contracts\MiddlewareInterface;
use Zoho\Crm\Contracts\RequestInterface;

/**
 * Middleware that validates requests.
 */
class Validation implements MiddlewareInterface
{
    /**
     * @inheritdoc
     *
     * @throws \Zoho\Crm\Exceptions\InvalidRequestException
     */
    public function __invoke(RequestInterface $request): void
    {
        // Additional internal validation logic
        $request->validate();
    }
}

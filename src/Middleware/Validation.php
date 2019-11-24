<?php

namespace Zoho\Crm\Middleware;

use Zoho\Crm\Client;
use Zoho\Crm\Contracts\MiddlewareInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Exceptions\UnsupportedModuleException;
use Zoho\Crm\Exceptions\UnsupportedMethodException;

/**
 * Middleware that validates queries.
 */
class Validation implements MiddlewareInterface
{
    /** @var \Zoho\Crm\Client The client to which the middleware is attached */
    protected $client;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Client $client The client to which the middleware is attached
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritdoc
     *
     * @throws \Zoho\Crm\Exceptions\InvalidQueryException
     * @throws \Zoho\Crm\Exceptions\UnsupportedModuleException
     * @throws \Zoho\Crm\Exceptions\UnsupportedMethodException
     */
    public function __invoke(QueryInterface $query): void
    {
        // Internal validation logic
        $query->validate();

        // Check if the requested module and method are both supported
        if (! $this->client->supports($query->getModule())) {
            throw new UnsupportedModuleException($query->getModule());
        }

        if (! $this->client->supportsMethod($query->getMethod())) {
            throw new UnsupportedMethodException($query->getMethod());
        }

        // Check that the method can be used on the module
        if (! $this->client->module($query->getModule())->supports($query->getMethod())) {
            throw new UnsupportedMethodException($query->getMethod(), $query->getModule());
        }
    }
}

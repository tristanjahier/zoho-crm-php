<?php

namespace Zoho\Crm\Middleware;

use Zoho\Crm\Client;
use Zoho\Crm\Contracts\MiddlewareInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\Exceptions\InvalidQueryException;
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
        // Analyze the URI path and check that it is correctly formed
        $uriPathSegments = Helper::getUrlPathSegments($query->getUri());

        if (count($uriPathSegments) != 3) {
            throw new InvalidQueryException($query, 'malformed URI.');
        }

        [$format, $module, $method] = $uriPathSegments;

        // Check if the requested module and method are both supported
        if (! $this->client->supports($module)) {
            throw new UnsupportedModuleException($module);
        }

        if (! $this->client->supportsMethod($method)) {
            throw new UnsupportedMethodException($method);
        }

        // Check that the method can be used on the module
        if (! $this->client->module($module)->supports($method)) {
            throw new UnsupportedMethodException($method, $module);
        }

        // Additional internal validation logic
        $query->validate();
    }
}

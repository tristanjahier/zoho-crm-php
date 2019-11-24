<?php

namespace Zoho\Crm\Middleware;

use Zoho\Crm\Client;
use Zoho\Crm\Contracts\MiddlewareInterface;
use Zoho\Crm\Contracts\QueryInterface;

/**
 * Middleware that adds the API authentication token to the query parameters.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/using-authentication-token.html
 */
class Authentication implements MiddlewareInterface
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
     */
    public function __invoke(QueryInterface $query): void
    {
        $query->param('authtoken', $this->client->getAuthToken());
    }
}

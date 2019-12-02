<?php

namespace Zoho\Crm\V1\Middleware;

use Zoho\Crm\V1\Client;
use Zoho\Crm\Contracts\MiddlewareInterface;
use Zoho\Crm\Contracts\QueryInterface;

/**
 * Middleware that adds the API authentication token to the query parameters.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/using-authentication-token.html
 */
class Authentication implements MiddlewareInterface
{
    /** @var \Zoho\Crm\V1\Client The client to which the middleware is attached */
    protected $client;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\V1\Client $client The client to which the middleware is attached
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
        $query->setUriParameter('authtoken', $this->client->getAuthToken());
    }
}

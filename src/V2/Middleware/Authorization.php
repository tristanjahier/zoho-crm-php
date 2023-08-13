<?php

namespace Zoho\Crm\V2\Middleware;

use Zoho\Crm\Contracts\MiddlewareInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\V2\Client;

/**
 * Middleware that adds the API authorization access token to the query headers.
 */
class Authorization implements MiddlewareInterface
{
    /** @var \Zoho\Crm\V2\Client The client to which the middleware is attached */
    protected $client;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\V2\Client $client The client to which the middleware is attached
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
        $query->setHeader('Authorization', 'Zoho-oauthtoken ' . $this->client->getAccessToken());
    }
}

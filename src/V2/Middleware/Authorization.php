<?php

namespace Zoho\Crm\V2\Middleware;

use Zoho\Crm\Contracts\MiddlewareInterface;
use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\V2\Client;

/**
 * Middleware that adds the API authorization access token to the request headers.
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
    public function __invoke(RequestInterface $request): void
    {
        $request->setHeader('Authorization', 'Zoho-oauthtoken ' . $this->client->getAccessToken());
    }
}

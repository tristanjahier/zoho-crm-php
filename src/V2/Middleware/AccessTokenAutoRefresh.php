<?php

namespace Zoho\Crm\V2\Middleware;

use Zoho\Crm\Contracts\MiddlewareInterface;
use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\V2\Client;

/**
 * Middleware that refreshes the API authorization access token below a limit of validity time.
 */
class AccessTokenAutoRefresh implements MiddlewareInterface
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
        if ($this->client->preferences()->isSet('access_token_auto_refresh_limit')) {
            $limit = $this->client->preferences()->get('access_token_auto_refresh_limit');
            $date = (new \DateTime())->modify("+{$limit} seconds");

            // If the token is null, has expired, or will expire within the given limit of time: refresh it!
            if (! $this->client->accessTokenIsValid() || $date > $this->client->getAccessTokenExpiryDate()) {
                $this->client->refreshAccessToken();
            }
        }
    }
}

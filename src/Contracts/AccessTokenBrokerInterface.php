<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

interface AccessTokenBrokerInterface
{
    /**
     * Get a fresh access token for the client to use.
     *
     * Returned value MUST be an ordered pair of the token (string) and the expiry date (DateTimeInterface).
     *
     * @return array{string, \DateTimeInterface}
     */
    public function getAccessTokenWithExpiryDate(): array;
}

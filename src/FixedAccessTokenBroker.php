<?php

declare(strict_types=1);

namespace Zoho\Crm;

use DateTimeImmutable;
use DateTimeInterface;
use Zoho\Crm\Contracts\AccessTokenBrokerInterface;

class FixedAccessTokenBroker implements AccessTokenBrokerInterface
{
    /** The API access token */
    protected string $accessToken;

    /** The access token expiry date */
    protected DateTimeImmutable $expiryDate;

    /**
     * The constructor.
     *
     * @param string $accessToken The fixed access token to serve
     * @param \DateTimeInterface $expiryDate The fixed expiry date to serve
     */
    public function __construct(string $accessToken, DateTimeInterface $expiryDate)
    {
        $this->accessToken = $accessToken;

        // Create an immutable copy from any type implementing DateTimeInterface.
        $this->expiryDate = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            $expiryDate->format(DateTimeInterface::ATOM)
        );
    }

    /**
     * @inheritdoc
     */
    public function getAccessTokenWithExpiryDate(): array
    {
        return [$this->accessToken, $this->expiryDate];
    }
}

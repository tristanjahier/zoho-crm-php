<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\AccessTokenStores;

use DateTimeInterface;
use DateTimeImmutable;

/**
 * Base implementation of an access token store.
 */
abstract class AbstractStore implements StoreInterface
{
    /** @var string|null The API access token */
    protected $accessToken;

    /** @var \DateTimeImmutable|null The access token expiry date */
    protected $expiryDate;

    /**
     * @inheritdoc
     */
    public function setAccessToken(?string $token): void
    {
        $this->accessToken = $token;
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @inheritdoc
     */
    public function setExpiryDate(?DateTimeInterface $date): void
    {
        if (is_null($date)) {
            $this->expiryDate = null;
            return;
        }

        // Create an immutable copy from any type implementing DateTimeInterface.
        $dateString = $date->format(DateTimeInterface::ATOM);
        $this->expiryDate = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $dateString);
    }

    /**
     * @inheritdoc
     */
    public function getExpiryDate(): ?DateTimeImmutable
    {
        return $this->expiryDate;
    }

    /**
     * @inheritdoc
     */
    public function isValid(): bool
    {
        return $this->accessToken !== null
            && (new DateTimeImmutable()) < $this->expiryDate;
    }

    /**
     * Determine if the access token has expired or not.
     *
     * Opposite of {@see self::isValid()}.
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        return ! $this->isValid();
    }

    /**
     * Check if the access token will expire in less than a given amount of time.
     *
     * @param int $howMuch The amount of time (> 0)
     * @param string $what The unit of time (supported by DateTime)
     * @return bool
     *
     * @throws \LogicException when $howMuch is negative
     */
    public function expiresInLessThan(int $howMuch, string $what): bool
    {
        if ($howMuch < 0) {
            throw new \LogicException('The amount of time cannot be negative.');
        }

        $date = (new \DateTime())->modify("+{$howMuch} {$what}");

        return is_null($this->accessToken) || $date > $this->expiryDate;
    }
}

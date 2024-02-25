<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * An interface to handle the storage of API access tokens.
 */
interface AccessTokenStoreInterface
{
    /**
     * Set the access token.
     *
     * @param string|null $token The new token
     */
    public function setAccessToken(?string $token);

    /**
     * Get the access token.
     */
    public function getAccessToken(): ?string;

    /**
     * Set the token expiry date.
     *
     * The date argument will not be modified.
     *
     * @param \DateTimeInterface|null $date The new expiry date
     */
    public function setExpiryDate(?DateTimeInterface $date);

    /**
     * Get the token expiry date.
     */
    public function getExpiryDate(): ?DateTimeImmutable;

    /**
     * Determine if the access token exists and is still valid or not.
     */
    public function isValid(): bool;

    /**
     * Save the current state back into the storage.
     */
    public function save(): bool;
}

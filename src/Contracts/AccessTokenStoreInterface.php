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
     * @return void
     */
    public function setAccessToken(?string $token): void;

    /**
     * Get the access token.
     *
     * @return string|null
     */
    public function getAccessToken(): ?string;

    /**
     * Set the token expiry date.
     *
     * The date argument will not be modified.
     *
     * @param \DateTimeInterface|null $date The new expiry date
     * @return void
     */
    public function setExpiryDate(?DateTimeInterface $date): void;

    /**
     * Get the token expiry date.
     *
     * @return \DateTimeImmutable|null
     */
    public function getExpiryDate(): ?DateTimeImmutable;

    /**
     * Determine if the access token exists and is still valid or not.
     *
     * @return bool
     */
    public function isValid(): bool;

    /**
     * Save the current state back into the storage.
     *
     * @return bool
     */
    public function save(): bool;
}

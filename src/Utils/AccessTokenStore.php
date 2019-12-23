<?php

namespace Zoho\Crm\Utils;

use DateTimeInterface;
use DateTimeImmutable;
use DateTime;

/**
 * A class to help handling a local cache store for API access token.
 * Especially helpful in development environement to avoid refreshing the token too often.
 */
class AccessTokenStore
{
    /** @var string The file path where the token must be cached/stored */
    protected $filePath;

    /** @var bool Whether the file exists or not */
    protected $fileExists;

    /** @var array The storage parsed content */
    protected $content;

    /**
     * The constructor.
     *
     * @param string $filePath The file path where to store the token
     * @param bool $load Whether to load the file or not
     */
    public function __construct(string $filePath, bool $load = true)
    {
        $this->filePath = $filePath;

        if ($load) {
            $this->load();
        }
    }

    /**
     * Read the file and load its contents if existing.
     *
     * @return void
     */
    public function load(): void
    {
        $this->fileExists = file_exists($this->filePath);
        $this->content = $this->fileExists ? json_decode(file_get_contents($this->filePath), true) : null;

        if (is_null($this->content)) {
            $this->content = [];
        }
    }

    /**
     * Alias of {@see self::load()}.
     *
     * @return void
     */
    public function reload(): void
    {
        $this->load();
    }

    /**
     * Determine if the file exists.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return $this->fileExists;
    }

    /**
     * Create the file if it does not exist.
     *
     * Return true if the creation was successful or if the file already exists.
     *
     * @return bool
     */
    public function createUnlessExists(): bool
    {
        if ($this->fileExists) {
            return true;
        }

        return $this->fileExists = touch($this->filePath);
    }

    /**
     * Set the access token.
     *
     * @param string $token The new token
     * @return void
     */
    public function setAccessToken(string $token): void
    {
        $this->content['access_token'] = $token;
    }

    /**
     * Get the access token.
     *
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return $this->content['access_token'] ?? null;
    }

    /**
     * Set the token expiry date.
     *
     * @param DateTimeInterface $date The new expiry date
     * @return void
     */
    public function setExpiryDate(DateTimeInterface $date): void
    {
        $this->content['expiry_date'] = $date->format(DateTimeInterface::RFC3339_EXTENDED);
    }

    /**
     * Get the token expiry date.
     *
     * @return DateTimeImmutable|null
     */
    public function getExpiryDate(): ?DateTimeImmutable
    {
        if (isset($this->content['expiry_date'])) {
            return new DateTimeImmutable($this->content['expiry_date']);
        }

        return null;
    }

    /**
     * Determine if the access token has expired or not.
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        $date = new DateTimeImmutable();

        return is_null($this->getAccessToken()) || $date > $this->getExpiryDate();
    }

    /**
     * Determine if the access token is still valid or not.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return ! $this->hasExpired();
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
            throw new \LogicException("The amount of time cannot be negative.");
        }

        $date = (new DateTime())->modify("+$howMuch $what");

        return is_null($this->getAccessToken()) || $date > $this->getExpiryDate();
    }

    /**
     * Save the current state back into the file.
     *
     * @return bool
     */
    public function save(): bool
    {
        $saved = (bool) file_put_contents($this->filePath, json_encode($this->content));

        if ($saved) {
            $this->fileExists = true;
        }

        return $saved;
    }
}

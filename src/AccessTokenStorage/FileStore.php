<?php

declare(strict_types=1);

namespace Zoho\Crm\AccessTokenStorage;

/**
 * A local file store for API access token.
 */
class FileStore extends AbstractStore
{
    /** The file path where the token must be cached/stored */
    protected string $filePath;

    /** Whether the file exists or not */
    protected bool $fileExists;

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
     */
    public function load(): void
    {
        $this->fileExists = file_exists($this->filePath);

        if (! $this->fileExists) {
            return;
        }

        $data = json_decode(file_get_contents($this->filePath), true);

        $this->setAccessToken($data['access_token'] ?? null);

        if (isset($data['expiry_date'])) {
            $this->setExpiryDate(new \DateTime($data['expiry_date']));
        }
    }

    /**
     * Alias of {@see self::load()}.
     */
    public function reload(): void
    {
        $this->load();
    }

    /**
     * Determine if the file exists.
     */
    public function exists(): bool
    {
        return $this->fileExists;
    }

    /**
     * Create the file if it does not exist.
     *
     * Return true if the creation was successful or if the file already exists.
     */
    public function createUnlessExists(): bool
    {
        if ($this->fileExists) {
            return true;
        }

        return $this->fileExists = touch($this->filePath);
    }

    /**
     * @inheritdoc
     */
    public function save(): bool
    {
        $data = json_encode([
            'access_token' => $this->accessToken,
            'expiry_date' => $this->expiryDate->format(\DateTimeInterface::ATOM)
        ]);

        $saved = (bool) file_put_contents($this->filePath, $data);

        if ($saved) {
            $this->fileExists = true;
        }

        return $saved;
    }
}

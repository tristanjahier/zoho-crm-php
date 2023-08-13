<?php

namespace Zoho\Crm\V2\AccessTokenStores;

/**
 * A local file store for API access token.
 */
class FileStore extends AbstractStore
{
    /** @var string The file path where the token must be cached/stored */
    protected $filePath;

    /** @var bool Whether the file exists or not */
    protected $fileExists;

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

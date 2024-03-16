<?php

declare(strict_types=1);

namespace Zoho\Crm\Traits;

/**
 * Basic implementation of HTTP request headers for RequestInterface.
 */
trait HasRequestHeaders
{
    /**
     * The array of HTTP request headers.
     *
     * @var array<string, string>
     */
    protected array $headers = [];

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setHeader(string $name, string $value): static
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function removeHeader(string $name): static
    {
        unset($this->headers[$name]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}

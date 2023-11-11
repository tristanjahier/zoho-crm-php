<?php

declare(strict_types=1);

namespace Zoho\Crm\Traits;

/**
 * Basic implementation of HTTP request headers for RequestInterface.
 */
trait HasRequestHeaders
{
    /** @var string[] The array of HTTP request headers */
    protected $headers = [];

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setHeader(string $name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function removeHeader(string $name)
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

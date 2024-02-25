<?php

declare(strict_types=1);

namespace Zoho\Crm;

use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Support\UrlParameters;

/**
 * A basic object representing an API request.
 */
class RawRequest implements RequestInterface
{
    use Traits\BasicRequestImplementation;
    use Traits\HasRequestHttpMethod;
    use Traits\HasRequestUrlParameters;

    /** The URL path */
    protected ?string $urlPath = null;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Contracts\ClientInterface $client The client to use to make the request
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Set the URL to request.
     *
     * @param string|null $url The new URL
     * @return $this
     */
    public function setUrl(?string $url): static
    {
        $url = $url ?? '';
        $this->urlPath = parse_url($url, PHP_URL_PATH);
        $this->urlParameters = UrlParameters::createFromUrl($url);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrlPath(): string
    {
        return $this->urlPath;
    }

    /**
     * @inheritdoc
     */
    public function validate(): void
    {
        // No specific validation
    }

    /**
     * @inheritdoc
     */
    public function getResponseTransformer(): ?ResponseTransformerInterface
    {
        // No specific transformation
        return null;
    }

    /**
     * Allow the deep cloning of the request.
     */
    public function __clone(): void
    {
        $this->urlParameters = clone $this->urlParameters;
    }
}

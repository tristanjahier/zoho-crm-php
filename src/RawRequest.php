<?php

namespace Zoho\Crm;

use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Support\HttpMethod;
use Zoho\Crm\Support\UrlParameters;

/**
 * A basic object representing an API request.
 */
class RawRequest implements RequestInterface
{
    use Traits\BasicRequestImplementation;
    use Traits\HasRequestHttpMethod;
    use Traits\HasRequestUrlParameters;

    /** @var string|null The URL path */
    protected $urlPath;

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
    public function setUrl(?string $url)
    {
        $url = $url ?? '';
        $this->urlPath = parse_url($url, PHP_URL_PATH);
        $this->urlParameters = UrlParameters::createFromUrl($url);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return "$this->urlPath?$this->urlParameters";
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
     *
     * @return void
     */
    public function __clone()
    {
        $this->urlParameters = clone $this->urlParameters;
    }
}

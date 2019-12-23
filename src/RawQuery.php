<?php

namespace Zoho\Crm;

use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Support\HttpMethod;
use Zoho\Crm\Support\UrlParameters;

/**
 * A basic object representing an API request.
 */
class RawQuery implements QueryInterface
{
    use Traits\BasicQueryImplementation;
    use Traits\HasRequestHttpMethod;

    /** @var string|null The URL */
    protected $url;

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
     * @inheritdoc
     *
     * @return $this
     */
    public function setUrl(?string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setUrlParameter(string $key, $value)
    {
        $path = parse_url($this->url, PHP_URL_PATH);
        $parameters = UrlParameters::createFromUrl($this->url);
        $parameters[$key] = $value;

        $this->url = $path . '?' . $parameters;

        return $this;
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
}

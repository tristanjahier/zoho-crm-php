<?php

namespace Zoho\Crm;

use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Support\HttpVerb;
use Zoho\Crm\Support\UrlParameters;

/**
 * A basic object representing an API request.
 */
class RawQuery implements QueryInterface
{
    use Traits\BasicQueryImplementation;
    use Traits\HasHttpVerb;

    /** @var string|null The URI */
    protected $uri;

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
    public function setUri(?string $uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setUriParameter(string $key, $value)
    {
        $path = parse_url($this->uri, PHP_URL_PATH);
        $parameters = UrlParameters::createFromUrl($this->uri);
        $parameters[$key] = $value;

        $this->uri = $path . '?' . $parameters;

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

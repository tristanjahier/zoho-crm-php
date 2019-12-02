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
    /** @var \Zoho\Crm\Contracts\ClientInterface The API client that originated this query */
    protected $client;

    /** @var string The HTTP verb/method */
    protected $httpVerb = HttpVerb::GET;

    /** @var string|null The URI */
    protected $uri;

    /** @var string[] The array of HTTP request headers */
    protected $headers = [];

    /** @var mixed $body The HTTP request body */
    protected $body;

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
     * Set the HTTP verb/method.
     *
     * @param string $verb The HTTP verb/method to use
     * @return $this
     */
    public function setHttpVerb(string $verb)
    {
        $this->httpVerb = $verb;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHttpVerb(): string
    {
        return $this->httpVerb;
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

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setBody($content)
    {
        $this->body = $content;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->body;
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
    public function copy(): QueryInterface
    {
        return clone $this;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResponseInterface
    {
        return $this->client->executeQuery($this);
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        return $this->execute()->getContent();
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

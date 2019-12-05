<?php

namespace Zoho\Crm;

use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Support\HttpVerb;

/**
 * A trait that contains a basic implementation for most of the QueryInterface features.
 */
trait BasicQueryTrait
{
    /** @var \Zoho\Crm\Contracts\ClientInterface The API client that originated this query */
    protected $client;

    /** @var string The HTTP verb/method */
    protected $httpVerb = HttpVerb::GET;

    /** @var string[] The array of HTTP request headers */
    protected $headers = [];

    /** @var mixed $body The HTTP request body */
    protected $body;

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
}

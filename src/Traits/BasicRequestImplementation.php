<?php

namespace Zoho\Crm\Traits;

use Zoho\Crm\Contracts\PaginatedRequestInterface;
use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Contracts\ClientInterface;

/**
 * A trait that contains a basic implementation for most of the RequestInterface features.
 */
trait BasicRequestImplementation
{
    use HasRequestHeaders, HasRequestBody;

    /** @var \Zoho\Crm\Contracts\ClientInterface The API client that originated this request */
    protected $client;

    /**
     * @inheritdoc
     */
    public function copy(): RequestInterface
    {
        return clone $this;
    }

    /**
     * @inheritdoc
     */
    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResponseInterface
    {
        return $this->client->executeRequest($this);
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        return $this->execute()->getContent();
    }

    /**
     * Execute the request and get the raw HTTP response(s).
     *
     * @return \Psr\Http\Message\ResponseInterface|\Psr\Http\Message\ResponseInterface[]
     */
    public function getRaw()
    {
        $responses = $this->execute()->getRawResponses();

        return $this instanceof PaginatedRequestInterface && $this->mustBePaginatedAutomatically()
            ? $responses
            : array_values($responses)[0];
    }
}

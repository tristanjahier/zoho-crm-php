<?php

declare(strict_types=1);

namespace Zoho\Crm\Traits;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\PaginatedRequestInterface;
use Zoho\Crm\Contracts\ResponseInterface;

/**
 * A trait that contains a basic implementation for most of the RequestInterface features.
 */
trait BasicRequestImplementation
{
    use HasRequestHeaders, HasRequestBody;

    /** @var \Zoho\Crm\Contracts\ClientInterface The API client that originated this request */
    protected ClientInterface $client;

    /**
     * @inheritdoc
     */
    public function copy(): static
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
    public function get(): mixed
    {
        return $this->execute()->getContent();
    }

    /**
     * Execute the request and get the raw HTTP response(s).
     *
     * @return \Psr\Http\Message\ResponseInterface|\Psr\Http\Message\ResponseInterface[]|null
     */
    public function getRaw(): HttpResponseInterface|array|null
    {
        $responses = $this->execute()->getRawResponses();

        return $this instanceof PaginatedRequestInterface && $this->mustBePaginatedAutomatically()
            ? $responses
            : array_values($responses)[0] ?? null;
    }
}

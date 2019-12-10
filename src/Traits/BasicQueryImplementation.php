<?php

namespace Zoho\Crm\Traits;

use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Contracts\ClientInterface;

/**
 * A trait that contains a basic implementation for most of the QueryInterface features.
 */
trait BasicQueryImplementation
{
    use HasRequestHeaders, HasRequestBody;

    /** @var \Zoho\Crm\Contracts\ClientInterface The API client that originated this query */
    protected $client;

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
    public function getClient(): ClientInterface
    {
        return $this->client;
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
     * Execute the query and get the raw, unparsed response.
     *
     * @return string|string[]
     */
    public function getRaw()
    {
        return $this->execute()->getRawContent();
    }
}

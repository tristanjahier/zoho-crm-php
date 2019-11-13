<?php

namespace Zoho\Crm;

use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Exceptions\UnsupportedModuleException;
use Zoho\Crm\Exceptions\UnsupportedMethodException;

/**
 * The query validator.
 */
class QueryValidator
{
    /** @var \Zoho\Crm\Contracts\ClientInterface The client to which this validator is attached */
    protected $client;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Contracts\ClientInterface $client The client to which it is attached
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Validate that a query is valid before sending the request to the API.
     *
     * @param \Zoho\Crm\Contracts\QueryInterface $query The query to validate
     * @return void
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedModuleException
     * @throws \Zoho\Crm\Exceptions\UnsupportedMethodException
     */
    public function validate(QueryInterface $query): void
    {
        // Internal validation logic
        $query->validate();

        // Check if the requested module and method are both supported
        if (! $this->client->supports($query->getModule())) {
            throw new UnsupportedModuleException($query->getModule());
        }

        if (! $this->client->supportsMethod($query->getMethod())) {
            throw new UnsupportedMethodException($query->getMethod());
        }
    }
}

<?php

namespace Zoho\Crm\V2;

/**
 * Base class of the sub-API helpers.
 */
abstract class AbstractSubApi
{
    /** @var \Zoho\Crm\V2\Client The client to which the sub-API is attached */
    protected $client;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\V2\Client $client The client to which the sub-API is attached
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}

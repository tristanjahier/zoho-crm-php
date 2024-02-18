<?php

declare(strict_types=1);

namespace Zoho\Crm\V2;

/**
 * Base class of the sub-API helpers.
 */
abstract class AbstractSubApi
{
    /** The client to which the sub-API is attached */
    protected Client $client;

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

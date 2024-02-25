<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Users;

use Zoho\Crm\V2\AbstractSubApi;

/**
 * Helper for the Users APIs.
 */
class SubApi extends AbstractSubApi
{
    /**
     * Create a request to list the users.
     */
    public function newListRequest(): ListRequest
    {
        return new ListRequest($this->client);
    }

    /**
     * Create an auto-paginated request to retrieve all the users.
     */
    public function all(): ListRequest
    {
        return $this->newListRequest()->autoPaginated();
    }
}

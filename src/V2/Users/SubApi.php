<?php

namespace Zoho\Crm\V2\Users;

use Zoho\Crm\V2\AbstractSubApi;

/**
 * Helper for the Users APIs.
 */
class SubApi extends AbstractSubApi
{
    /**
     * Create a query to list the users.
     *
     * @return ListQuery
     */
    public function newListQuery(): ListQuery
    {
        return new ListQuery($this->client);
    }

    /**
     * Create an auto-paginated query to retrieve all the users.
     *
     * @return ListQuery
     */
    public function all(): ListQuery
    {
        return $this->newListQuery()->autoPaginated();
    }
}

<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Query;

interface MethodInterface
{
    /**
     * Determine if a response contains data.
     *
     * @param array $response The parsed response
     * @param \Zoho\Crm\Api\Query $query The query that has been executed
     * @return bool
     */
    public function responseContainsData(array $response, Query $query);

    /**
     * Clean the response content to keep only the worthy data.
     *
     * @param array $response The parsed response
     * @param \Zoho\Crm\Api\Query $query The query that has been executed
     * @return mixed
     */
    public function tidyResponse(array $response, Query $query);

    /**
     * Determine if this API method should return multiple records.
     *
     * @param \Zoho\Crm\Api\Query $query The query that has been executed
     * @return bool
     */
    public function expectsMultipleRecords(Query $query);
}

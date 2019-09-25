<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Query;

interface MethodInterface
{
    /**
     * Determine if a raw API response is empty or null (no data).
     *
     * @param array $response The parsed response
     * @param \Zoho\Crm\Api\Query $query The origin query
     * @return bool
     */
    public function isResponseEmpty(array $response, Query $query);

    /**
     * Get the value to return for empty/null API responses.
     *
     * @param \Zoho\Crm\Api\Query $query The origin query
     * @return mixed
     */
    public function getEmptyResponse(Query $query);

    /**
     * Clean the response content to keep only the worthy data.
     *
     * @param array $response The parsed response
     * @param \Zoho\Crm\Api\Query $query The origin query
     * @return mixed
     */
    public function cleanResponse(array $response, Query $query);

    /**
     * Convert the clean response into an adapted data type or object.
     *
     * @param mixed $response The cleaned response
     * @param \Zoho\Crm\Api\Query $query The origin query
     * @return mixed
     */
    public function convertResponse($response, Query $query);
}

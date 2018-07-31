<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Query;

class GetRecordById extends GetRecords
{
    public static function tidyResponse(array $response, Query $query)
    {
        $result = parent::tidyResponse($response, $query);
        // Unwrap in case of single element
        return self::expectsMultipleRecords($query) ? $result : $result[0];
    }

    public static function expectsMultipleRecords(Query $query = null)
    {
        return isset($query) ? $query->hasParameter('idlist') : false;
    }
}

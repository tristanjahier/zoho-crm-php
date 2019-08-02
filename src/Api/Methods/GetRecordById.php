<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Query;

/**
 * @see https://www.zoho.com/crm/developer/docs/api/getrecordbyid.html
 */
class GetRecordById extends GetRecords
{
    /**
     * @inheritdoc
     */
    public static function tidyResponse(array $response, Query $query)
    {
        $result = parent::tidyResponse($response, $query);
        // Unwrap in case of single element
        return self::expectsMultipleRecords($query) ? $result : $result[0];
    }

    /**
     * @inheritdoc
     */
    public static function expectsMultipleRecords(Query $query = null)
    {
        return isset($query) ? $query->hasParameter('idlist') : false;
    }
}

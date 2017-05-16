<?php

namespace Zoho\CRM\Api\Methods;

use Zoho\CRM\Api\Request;
use Zoho\CRM\Api\ResponseDataType;

class GetDeletedRecordIds extends AbstractMethod
{
    protected static $response_type = ResponseDataType::OTHER;

    public static function responseContainsData(array $response)
    {
        if (isset($response['response']['nodata'])) {
            return false;
        }

        // When going beyond the last page of results,
        // the API does not respond with a "nodata" message, as you would expect.
        // It just returns 'true' as the content of "DeletedIDs"... i.e.: {"DeletedIDs": true}
        $result = $response['response']['result']['DeletedIDs'];

        return isset($result) && ! empty($result) && $result !== true;
    }

    public static function tidyResponse(array $response, Request $request)
    {
        // The result is a big string which contains IDs separated by comas
        $ids = array_filter(explode(',', $response['response']['result']['DeletedIDs']));

        return count($ids) > 0 ? $ids : null;
    }
}

<?php

namespace Zoho\CRM\Api\Methods;

use Zoho\CRM\Api\Request;
use Zoho\CRM\Api\ResponseDataType;

class GetDeletedRecordIds extends AbstractMethod
{
    protected static $response_type = ResponseDataType::OTHER;

    public static function responseContainsData(array $response)
    {
        $result = $response['response']['result']['DeletedIDs'];

        // When there is no (more) deleted ID, it just returns 'true'. i.e.: {"DeletedIDs":true}
        return isset($result) && ! empty($result) && $result !== true;
    }

    public static function tidyResponse(array $response, Request $request)
    {
        // The result is a big string which contains IDs separated by comas
        $ids = array_filter(explode(',', $response['response']['result']['DeletedIDs']));

        return count($ids) > 0 ? $ids : null;
    }
}

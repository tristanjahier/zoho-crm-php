<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\ResponseDataType;
use Zoho\Crm\Api\Query;

class GetModules extends AbstractMethod
{
    protected static $response_type = ResponseDataType::OTHER;

    public static function tidyResponse(array $response, Query $query)
    {
        $entries = [];

        foreach ($response['response']['result']['row'] as $row) {
            $entries[] = $row;
        }

        return $entries;
    }
}

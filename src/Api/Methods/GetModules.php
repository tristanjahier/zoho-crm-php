<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\ResponseDataType;
use Zoho\Crm\Api\Request;

class GetModules extends AbstractMethod
{
    protected static $response_type = ResponseDataType::OTHER;

    public static function tidyResponse(array $response, Request $request)
    {
        $entries = [];

        foreach ($response['response']['result']['row'] as $row) {
            $entries[] = $row;
        }

        return $entries;
    }
}

<?php

namespace Zoho\CRM\Api\Methods;

use Zoho\CRM\Api\ResponseDataType;
use Zoho\CRM\Api\Request;

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

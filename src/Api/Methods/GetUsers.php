<?php

namespace Zoho\CRM\Api\Methods;

use Zoho\CRM\Api\ResponseDataType;
use Zoho\CRM\Api\Request;

class GetUsers extends AbstractMethod
{
    protected static $response_type = ResponseDataType::OTHER;

    public static function responseContainsData(array $response)
    {
        return ! isset($response['users']['nodata']);
    }

    public static function tidyResponse(array $response, Request $request)
    {
        $entries = [];

        foreach ($response['users']['user'] as $user)
            $entries[] = $user;

        return $entries;
    }
}

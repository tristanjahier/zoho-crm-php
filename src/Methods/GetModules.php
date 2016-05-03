<?php

namespace Zoho\CRM\Methods;

use Zoho\CRM\Core\ApiResponseType;
use Zoho\CRM\Core\Request;

class GetModules extends AbstractMethod
{
    protected static $response_type = ApiResponseType::OTHER;

    public static function tidyResponse(array $response, Request $request)
    {
        $entries = [];

        foreach ($response['response']['result']['row'] as $row)
            $entries[] = $row;

        return $entries;
    }
}

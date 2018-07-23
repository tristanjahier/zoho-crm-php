<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Request;
use Zoho\Crm\Api\ResponseDataType;

class DeleteFile extends AbstractMethod
{
    protected static $response_type = ResponseDataType::OTHER;

    public static function tidyResponse(array $response, Request $request)
    {
        return isset($response['response']['success']);
    }
}

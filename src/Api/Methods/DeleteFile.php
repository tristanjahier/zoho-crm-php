<?php

namespace Zoho\CRM\Api\Methods;

use Zoho\CRM\Api\Request;
use Zoho\CRM\Api\ResponseDataType;

class DeleteFile extends AbstractMethod
{
    protected static $response_type = ResponseDataType::OTHER;

    public static function tidyResponse(array $response, Request $request)
    {
        return isset($response['response']['success']);
    }
}

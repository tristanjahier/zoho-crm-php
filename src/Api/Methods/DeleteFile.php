<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Query;
use Zoho\Crm\Api\ResponseDataType;

class DeleteFile extends AbstractMethod
{
    protected static $response_type = ResponseDataType::OTHER;

    public static function tidyResponse(array $response, Query $query)
    {
        return isset($response['response']['success']);
    }
}

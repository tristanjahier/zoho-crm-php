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

        $users = $response['users']['user'];

        // Single user or multiple users?
        // If single user: wrap it in an array to process it generically
        if (isset($users['id'])) {
            $users = [$users];
        }

        foreach ($users as $user) {
            $entries[] = $user;
        }

        return $entries;
    }
}

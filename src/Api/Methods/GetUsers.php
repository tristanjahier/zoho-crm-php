<?php

namespace Zoho\CRM\Api\Methods;

use Zoho\CRM\Api\Request;

class GetUsers extends AbstractMethod
{
    public static function tidyResponse(array $response, Request $request)
    {
        $entries = [];

        foreach ($response['users']['user'] as $user)
            $entries[] = $user;

        return $entries;
    }
}

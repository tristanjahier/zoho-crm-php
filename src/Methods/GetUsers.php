<?php

namespace Zoho\CRM\Methods;

class GetUsers extends AbstractMethod
{
    public static function tidyResponse(array $response, $module)
    {
        $entries = [];

        foreach ($response['users']['user'] as $user)
            $entries[] = $user;

        return $entries;
    }
}

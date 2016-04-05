<?php

namespace Zoho\CRM\Methods;

class GetModules extends AbstractMethod
{
    public static function tidyResponse(array $response, $module)
    {
        $entries = [];

        foreach ($response['response']['result']['row'] as $row)
            $entries[] = $row;

        return $entries;
    }
}

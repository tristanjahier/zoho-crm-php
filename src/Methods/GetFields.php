<?php

namespace Zoho\CRM\Methods;

class GetFields extends AbstractMethod
{
    public static function tidyResponse(array $response, $module)
    {
        return $response[$module]['section'];
    }
}

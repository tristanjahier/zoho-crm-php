<?php

namespace Zoho\CRM\Methods;

use Zoho\CRM\Core\ApiResponseType;
use Zoho\CRM\Core\Request;

class GetFields extends AbstractMethod
{
    protected static $response_type = ApiResponseType::OTHER;

    public static function tidyResponse(array $response, Request $request)
    {
        return $response[$request->getModule()]['section'];
    }
}

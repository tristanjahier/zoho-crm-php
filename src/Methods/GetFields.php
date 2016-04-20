<?php

namespace Zoho\CRM\Methods;

use Zoho\CRM\Core\Request;

class GetFields extends AbstractMethod
{
    public static function tidyResponse(array $response, Request $request)
    {
        return $response[$request->getModule()]['section'];
    }
}

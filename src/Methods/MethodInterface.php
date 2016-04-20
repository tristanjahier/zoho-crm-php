<?php

namespace Zoho\CRM\Methods;

use Zoho\CRM\Core\Request;

interface MethodInterface
{
    public static function tidyResponse(array $response, Request $request);

    public static function expectsMultipleRecords($request);
}

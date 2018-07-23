<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Request;

interface MethodInterface
{
    public static function responseContainsData(array $response);

    public static function tidyResponse(array $response, Request $request);

    public static function expectsMultipleRecords($request);
}

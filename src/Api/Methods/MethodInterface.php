<?php

namespace Zoho\CRM\Api\Methods;

use Zoho\CRM\Api\Request;

interface MethodInterface
{
    public static function responseContainsData(array $response);

    public static function tidyResponse(array $response, Request $request);

    public static function expectsMultipleRecords($request);
}

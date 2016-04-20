<?php

namespace Zoho\CRM\Methods;

use Zoho\CRM\Core\Request;

class GetRecordById extends GetRecords
{
    public static function tidyResponse(array $response, Request $request)
    {
        $result = parent::tidyResponse($response, $request);
        // Unwrap in case of single element
        return self::expectsMultipleRecords($request) ? $result : $result[0];
    }

    public static function expectsMultipleRecords($request = null)
    {
        return isset($request) ? $request->getParameters()->contains('idlist') : false;
    }
}

<?php

namespace Zoho\CRM\Methods;

use Zoho\CRM\Core\Request;

class GetRecordById extends GetRecords
{
    public static function tidyResponse(array $response, Request $request)
    {
        $result = parent::tidyResponse($response, $request);
        // Unwrap in case of single element
        return count($result) === 1 ? $result[0] : $result;
    }

    public static function expectsMultipleRecords($request = null)
    {
        return isset($request) ? $request->getParameters()->contains('idlist') : false;
    }
}

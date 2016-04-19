<?php

namespace Zoho\CRM\Methods;

class GetRecordById extends GetRecords
{
    public static function tidyResponse(array $response, $module)
    {
        $result = parent::tidyResponse($response, $module);
        // Unwrap in case of single element
        return count($result) === 1 ? $result[0] : $result;
    }

    public static function expectsMultipleRecords($request = null)
    {
        return isset($request) ? $request->getParameters()->contains('idlist') : false;
    }
}

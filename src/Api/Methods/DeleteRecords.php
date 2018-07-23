<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Request;
use Zoho\Crm\Api\ResponseDataType;

class DeleteRecords extends AbstractMethod
{
    protected static $response_type = ResponseDataType::OTHER;

    public static function tidyResponse(array $response, Request $request)
    {
        // There is nothing to do with the response because it is ALWAYS the same:
        // "result": {
        //   "code": "5000",
        //   "message": "Record Id(s) : <RecordId1>;<RecordId2>;...,Record(s) deleted successfully"
        // }

        // The API always answers as if it was succesful, even if the record does not exist
        // or has already been deleted! Thus, we have no choice but to simply return null.

        return null;
    }
}

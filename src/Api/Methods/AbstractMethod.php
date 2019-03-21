<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\ResponseDataType;
use Zoho\Crm\Api\HttpVerb;
use Zoho\Crm\Api\Query;
use Zoho\Crm\Support\ClassShortNameTrait;

abstract class AbstractMethod implements MethodInterface
{
    use ClassShortNameTrait;

    protected static $response_type = ResponseDataType::RECORDS;

    protected static $multiple_records = true;

    protected static $http_verb = HttpVerb::GET;

    public static function name()
    {
        return lcfirst(self::getClassShortName());
    }

    public static function getResponseDataType()
    {
        return static::$response_type;
    }

    public static function expectsMultipleRecords(Query $query = null)
    {
        return static::$multiple_records;
    }

    public static function getHttpVerb()
    {
        return static::$http_verb;
    }

    public static function responseContainsData(array $response, Query $query)
    {
        return ! isset($response['response']['nodata']);
    }
}

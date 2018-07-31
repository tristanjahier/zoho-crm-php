<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\ResponseDataType;
use Zoho\Crm\Api\HttpVerb;
use Zoho\Crm\Api\Query;

abstract class AbstractMethod implements MethodInterface
{
    protected static $response_type = ResponseDataType::RECORDS;

    protected static $multiple_records = true;

    protected static $http_verb = HttpVerb::GET;

    final public static function getMethodName()
    {
        return (new \ReflectionClass(static::class))->getShortName();
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

    public static function responseContainsData(array $response)
    {
        return ! isset($response['response']['nodata']);
    }
}

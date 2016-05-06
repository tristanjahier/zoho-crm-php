<?php

namespace Zoho\CRM\Api\Methods;

use Zoho\CRM\Api\ResponseDataType;
use Zoho\CRM\Api\HttpVerb;

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

    public static function expectsMultipleRecords($request = null)
    {
        return static::$multiple_records;
    }

    public static function getHttpVerb()
    {
        return static::$http_verb;
    }
}

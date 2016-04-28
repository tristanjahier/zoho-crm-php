<?php

namespace Zoho\CRM\Methods;

use Zoho\CRM\Core\ApiResponseType;
use Zoho\CRM\Core\HttpVerb;

abstract class AbstractMethod implements MethodInterface
{
    protected static $response_type = ApiResponseType::RECORDS;

    protected static $multiple_records = true;

    protected static $http_verb = HttpVerb::GET;

    final public static function getMethodName()
    {
        return (new \ReflectionClass(static::class))->getShortName();
    }

    public static function getResponseType()
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

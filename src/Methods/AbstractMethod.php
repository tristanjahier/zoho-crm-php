<?php

namespace Zoho\CRM\Methods;

abstract class AbstractMethod implements MethodInterface
{
    protected static $multiple_records = true;

    final public static function getMethodName()
    {
        return (new \ReflectionClass(static::class))->getShortName();
    }

    public static function expectsMultipleRecords($request = null)
    {
        return static::$multiple_records;
    }
}

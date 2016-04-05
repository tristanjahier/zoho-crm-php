<?php

namespace Zoho\CRM\Methods;

abstract class AbstractMethod implements MethodInterface
{
    final public static function getMethodName()
    {
        return (new \ReflectionClass(static::class))->getShortName();
    }
}

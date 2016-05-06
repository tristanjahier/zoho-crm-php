<?php

namespace Zoho\CRM\Api;

class ErrorHandler
{
    private static $exceptions = [
        '4600'  => Exception\InvalidParametersException::class,
        '4820'  => Exception\RateLimitExceededException::class,
        '4834'  => Exception\InvalidTicketIdException::class,
    ];

    public static function handle(array $error)
    {
        if (isset(self::$exceptions[$error['code']])) {
            $exception_type = self::$exceptions[$error['code']];
            throw new $exception_type($error['message']);
        } else {
            throw new Exception\GenericException($error['message'], $error['code']);
        }
    }
}

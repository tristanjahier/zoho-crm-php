<?php

namespace Zoho\Crm\Api;

class ErrorHandler
{
    private static $exceptions = [
        '4600'  => Exceptions\InvalidParametersException::class,
        '4820'  => Exceptions\RateLimitExceededException::class,
        '4834'  => Exceptions\InvalidTicketIdException::class,
        '4103'  => Exceptions\RecordNotFoundException::class,
        '4421'  => Exceptions\RequestLimitExceededException::class,
    ];

    public static function handle(array $error)
    {
        if (isset(self::$exceptions[$error['code']])) {
            $exception_type = self::$exceptions[$error['code']];
            throw new $exception_type($error['message']);
        } else {
            throw new Exceptions\GenericException($error['message'], $error['code']);
        }
    }
}

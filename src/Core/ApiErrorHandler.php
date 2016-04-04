<?php

namespace Zoho\CRM\Core;

use Zoho\CRM\Exception\Api as ApiException;

class ApiErrorHandler
{
    private static $exceptions = [
        '4600'  => ApiException\InvalidParametersException::class,
        '4820'  => ApiException\RateLimitExceededException::class,
        '4834'  => ApiException\InvalidTicketIdException::class,
    ];

    public static function handle(array $error)
    {
        if (isset(self::$exceptions[$error['code']])) {
            $exception_type = self::$exceptions[$error['code']];
            throw new $exception_type($error['message']);
        } else {
            throw new ApiException\GenericException($error['message'], $error['code']);
        }
    }
}

<?php

namespace Zoho\Crm\Api;

/**
 * Static class to handle API errors and cast them into appropriate PHP exceptions.
 */
class ErrorHandler
{
    /** @var array An associative array to match API error codes with exceptions */
    private static $exceptions = [
        '4600'  => Exceptions\InvalidParametersException::class,
        '4820'  => Exceptions\RateLimitExceededException::class,
        '4834'  => Exceptions\InvalidTicketIdException::class,
        '4103'  => Exceptions\RecordNotFoundException::class,
        '4421'  => Exceptions\RequestLimitExceededException::class,
    ];

    /**
     * Handle a parsed API error.
     *
     * @param array $error The API error
     * @return void
     *
     * @throws Exceptions\AbstractException
     */
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

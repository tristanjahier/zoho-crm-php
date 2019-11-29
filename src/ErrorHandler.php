<?php

namespace Zoho\Crm;

/**
 * Static class to handle API errors and cast them into appropriate PHP exceptions.
 */
class ErrorHandler
{
    /** @var array An associative array to match API error codes with exceptions */
    private static $exceptions = [
        '4600'  => Exceptions\Api\InvalidParametersException::class,
        '4820'  => Exceptions\Api\RateLimitExceededException::class,
        '4834'  => Exceptions\Api\InvalidTicketIdException::class,
        '4103'  => Exceptions\Api\RecordNotFoundException::class,
        '4421'  => Exceptions\Api\RequestLimitExceededException::class,
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
            $type = self::$exceptions[$error['code']];
            throw new $type($error['message']);
        }

        throw new Exceptions\Api\GenericException($error['message'], $error['code']);
    }
}

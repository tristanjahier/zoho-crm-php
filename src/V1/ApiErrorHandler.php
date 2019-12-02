<?php

namespace Zoho\Crm\V1;

use Zoho\Crm\Exceptions\Api as ApiExceptions;

/**
 * Static class to handle API errors and cast them into appropriate PHP exceptions.
 */
class ApiErrorHandler
{
    /** @var array An associative array to match API error codes with exceptions */
    private static $exceptions = [
        '4600'  => ApiExceptions\InvalidParametersException::class,
        '4820'  => ApiExceptions\RateLimitExceededException::class,
        '4834'  => ApiExceptions\InvalidTicketIdException::class,
        '4103'  => ApiExceptions\RecordNotFoundException::class,
        '4421'  => ApiExceptions\RequestLimitExceededException::class,
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

        throw new ApiExceptions\GenericException($error['message'], $error['code']);
    }
}

<?php

namespace Zoho\Crm\Support;

/**
 * Enumeration of the standard HTTP methods.
 */
abstract class HttpMethod
{
    /** @var string */
    const OPTIONS = 'OPTIONS';

    /** @var string */
    const GET = 'GET';

    /** @var string */
    const HEAD = 'HEAD';

    /** @var string */
    const POST = 'POST';

    /** @var string */
    const PUT = 'PUT';

    /** @var string */
    const DELETE = 'DELETE';

    /** @var string */
    const TRACE = 'TRACE';

    /** @var string */
    const CONNECT = 'CONNECT';

    /** @var string */
    const PATCH = 'PATCH';

    /**
     * Get all valid HTTP methods.
     *
     * @return string[]
     */
    public static function getAll(): array
    {
        return array_values((new \ReflectionClass(self::class))->getConstants());
    }

    /**
     * Check if a string is a valid HTTP method.
     *
     * @param string $method The string to check
     * @return bool
     */
    public static function isValid(string $method): bool
    {
        return in_array(strtoupper($method), self::getAll());
    }
}

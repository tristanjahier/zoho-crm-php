<?php

declare(strict_types=1);

namespace Zoho\Crm\Support;

/**
 * Enumeration of the standard HTTP methods.
 */
abstract class HttpMethod
{
    /** @var string */
    public const OPTIONS = 'OPTIONS';

    /** @var string */
    public const GET = 'GET';

    /** @var string */
    public const HEAD = 'HEAD';

    /** @var string */
    public const POST = 'POST';

    /** @var string */
    public const PUT = 'PUT';

    /** @var string */
    public const DELETE = 'DELETE';

    /** @var string */
    public const TRACE = 'TRACE';

    /** @var string */
    public const CONNECT = 'CONNECT';

    /** @var string */
    public const PATCH = 'PATCH';

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

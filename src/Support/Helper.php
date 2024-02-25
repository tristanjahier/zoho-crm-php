<?php

declare(strict_types=1);

namespace Zoho\Crm\Support;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

/**
 * Static helper class for miscellaneous purposes.
 */
final class Helper
{
    /** Cached instance of Doctrine Inflector */
    private static Inflector $inflector;

    /**
     * The constructor.
     *
     * It is private to prevent instanciation.
     */
    private function __construct()
    {
        //
    }

    /**
     * Get the string representation of a boolean value.
     *
     * @param bool $bool The boolean value
     */
    public static function booleanToString(bool $bool): string
    {
        return $bool ? 'true' : 'false';
    }

    /**
     * Get the boolean value corresponding to a string.
     *
     * @param string $bool A string representing a boolean
     *
     * @throws \Exception if the string is neither "true" nor "false"
     */
    public static function stringToBoolean(string $bool): bool
    {
        if ($bool === 'true') {
            return true;
        }

        if ($bool === 'false') {
            return false;
        }

        throw new \InvalidArgumentException("Invalid boolean string representation: '{$bool}'");
    }

    /**
     * Check if a string matches a given pattern.
     *
     * The pattern wildcard is the asterisk: "*".
     *
     * @param string $value The string to test
     * @param string $pattern The pattern
     */
    public static function stringIsLike(string $value, string $pattern): bool
    {
        if ($value === $pattern) {
            return true;
        }

        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards.
        $pattern = str_replace('\*', '.*', $pattern);

        return preg_match('#^'.$pattern.'\z#ui', $value) === 1;
    }

    /**
     * Make a string finish with a single instance of a given suffix.
     *
     * Note: adapted from Laravel (`Illuminate\Support\Str::finish`).
     *
     * @param string $value The string to modify
     * @param string $suffix The string value to have at the end
     */
    public static function finishString(string $value, string $suffix): string
    {
        $quoted = preg_quote($suffix, '/');

        return preg_replace('/(?:'.$quoted.')+$/u', '', $value) . $suffix;
    }

    /**
     * Get all segments of a URL path.
     *
     * @param string $url The URL to parse
     * @return string[]
     */
    public static function getUrlPathSegments(string $url): array
    {
        $path = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');

        return explode('/', $path);
    }

    /**
     * Get a segment of a URL path by index.
     *
     * @param string $url The URL to parse
     * @param int $index The segment index
     */
    public static function getUrlPathSegmentByIndex(string $url, int $index): ?string
    {
        $segments = self::getUrlPathSegments($url);

        return $segments[$index] ?? null;
    }

    /**
     * Check if a value is a valid date.
     *
     * It must be either an object implementing DateTimeInterface, or a valid date string.
     *
     * @param mixed $date The value to check
     */
    public static function isValidDateInput(string|\DateTimeInterface $date): bool
    {
        if ($date instanceof \DateTimeInterface) {
            return true;
        }

        if (! is_string($date) || trim($date) === '') {
            return false;
        }

        try {
            new \DateTime($date);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get an instance of Doctrine Inflector for string manipulations.
     */
    public static function inflector(): Inflector
    {
        if (isset(self::$inflector)) {
            return self::$inflector;
        }

        return self::$inflector = InflectorFactory::create()->build();
    }

    /**
     * Get the short name of a class.
     *
     * The short name (or unqualified name) is the class name without the namespace part.
     * This method avoids to use the reflection API: {@see \ReflectionClass::getShortName()}.
     *
     * @param class-string $className
     */
    public static function getClassShortName(string $className): string
    {
        if ($pos = strrchr($className, '\\')) {
            return substr($pos, 1);
        }

        return $className;
    }
}

<?php

namespace Zoho\Crm\Support;

use Exception;

/**
 * Static helper class for miscellaneous purposes.
 */
final class Helper
{
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
     * @return string
     */
    public static function booleanToString($bool)
    {
        return $bool ? 'true' : 'false';
    }

    /**
     * Get the boolean value corresponding to a string.
     *
     * @param string $bool A string representing a boolean
     * @return bool
     *
     * @throws \Exception if the string is neither "true" nor "false".
     */
    public static function stringToBoolean($bool)
    {
        if ($bool === 'true') {
            return true;
        } elseif ($bool === 'false') {
            return false;
        }

        throw new Exception('Invalid boolean string representation: "' . $bool . '"');
    }

    /**
     * Check if a string matches a given pattern.
     *
     * The pattern wildcard is the asterisk: "*".
     *
     * @param string $value The string to test
     * @param string $pattern The pattern
     * @return bool
     */
    public static function stringIsLike($value, $pattern)
    {
        if ($value === $pattern) {
            return true;
        }

        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards.
        $pattern = str_replace('\*', '.*', $pattern);

        return preg_match('#^'.$pattern.'\z#ui', $value) === 1;
    }
}

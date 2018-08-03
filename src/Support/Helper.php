<?php

namespace Zoho\Crm\Support;

use Exception;

final class Helper
{
    const BASE_NAMESPACE = 'Zoho\\Crm';

    private function __construct()
    {
        // Prevent instanciation.
    }

    public static function getModuleClass($name)
    {
        return self::BASE_NAMESPACE . '\\Api\\Modules\\' . ucfirst($name);
    }

    public static function getMethodClass($name)
    {
        return self::BASE_NAMESPACE . '\\Api\\Methods\\' . ucfirst($name);
    }

    public static function getEntityClass($name)
    {
        return self::BASE_NAMESPACE . '\\Entities\\' . ucfirst($name);
    }

    public static function booleanToString($bool)
    {
        return $bool ? 'true' : 'false';
    }

    public static function stringToBoolean($bool)
    {
        if ($bool === 'true') {
            return true;
        } elseif ($bool === 'false') {
            return false;
        }

        throw new Exception('Invalid boolean string representation: "' . $bool . '"');
    }

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

<?php

namespace Zoho\Crm\Support;

/**
 * Simple trait to get the short name of any class.
 */
trait ClassShortNameTrait
{
    /**
     * Get the short name of the class from where it is called.
     *
     * The short name (or unqualified name) is the class name without the namespace part.
     * This method avoids to use the reflection API: {@see \ReflectionClass::getShortName()}.
     *
     * @return string
     */
    protected static function getClassShortName()
    {
        if ($pos = strrchr(static::class, '\\')) {
            return substr($pos, 1);
        } else {
            return static::class;
        }
    }
}

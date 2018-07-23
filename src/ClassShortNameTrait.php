<?php

namespace Zoho\Crm;

trait ClassShortNameTrait
{
    protected static function getClassShortName()
    {
        if ($pos = strrchr(static::class, '\\')) {
            return substr($pos, 1);
        } else {
            return static::class;
        }
    }
}

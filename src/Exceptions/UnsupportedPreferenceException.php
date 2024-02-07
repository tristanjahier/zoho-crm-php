<?php

declare(strict_types=1);

namespace Zoho\Crm\Exceptions;

class UnsupportedPreferenceException extends Exception
{
    /**
     * The constructor.
     *
     * @param string $preference The preference key
     */
    public function __construct(string $preference)
    {
        parent::__construct("Preference '{$preference}' is not supported.");
    }
}

<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class UnsupportedPreferenceException extends Exception
{
    /**
     * The constructor.
     *
     * @param string $preference The preference key
     */
    public function __construct($preference)
    {
        parent::__construct("Preference '$preference' is not supported.");
    }
}

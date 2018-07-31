<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class UnsupportedPreferenceException extends Exception
{
    public function __construct($preference)
    {
        parent::__construct("Preference '$preference' is not supported.");
    }
}

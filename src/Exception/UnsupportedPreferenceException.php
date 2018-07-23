<?php

namespace Zoho\Crm\Exception;

class UnsupportedPreferenceException extends \Exception
{
    public function __construct($preference)
    {
        parent::__construct("Preference '$preference' is not supported.");
    }
}

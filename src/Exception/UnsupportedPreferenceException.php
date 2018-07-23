<?php

namespace Zoho\CRM\Exception;

class UnsupportedPreferenceException extends \Exception
{
    public function __construct($preference)
    {
        parent::__construct("Preference '$preference' is not supported.");
    }
}

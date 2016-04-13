<?php

namespace Zoho\CRM\Exception;

class UnsupportedClientPreferenceException extends \Exception
{
    public function __construct($preference)
    {
        parent::__construct("Client preference $preference is not supported.");
    }
}

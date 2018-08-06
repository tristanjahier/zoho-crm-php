<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class UnsupportedMethodException extends Exception
{
    public function __construct($method, $module = null)
    {
        $message = "Method $method is not supported";

        if (isset($module)) {
            $message .= " by module $module.";
        } else {
            $message .= '.';
        }

        parent::__construct($message);
    }
}

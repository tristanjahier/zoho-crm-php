<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class UnsupportedMethodException extends Exception
{
    public function __construct($module, $method)
    {
        parent::__construct("Method $method is not supported by module $module.");
    }
}

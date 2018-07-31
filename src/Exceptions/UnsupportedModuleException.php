<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class UnsupportedModuleException extends Exception
{
    public function __construct($module)
    {
        parent::__construct("Module $module is not supported.");
    }
}

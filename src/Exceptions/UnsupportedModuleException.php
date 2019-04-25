<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class UnsupportedModuleException extends Exception
{
    /**
     * The constructor.
     *
     * @param string $module The name of the module
     */
    public function __construct($module)
    {
        parent::__construct("Module $module is not supported.");
    }
}

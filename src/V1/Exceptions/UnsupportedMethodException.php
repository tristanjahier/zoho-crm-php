<?php

namespace Zoho\Crm\V1\Exceptions;

use Exception;

class UnsupportedMethodException extends Exception
{
    /**
     * The constructor.
     *
     * @param string $method The name of the method
     * @param string $module (optional) The name of the module
     */
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

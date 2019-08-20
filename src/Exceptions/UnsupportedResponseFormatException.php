<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class UnsupportedResponseFormatException extends Exception
{
    /**
     * The constructor.
     *
     * @param string $format The format
     */
    public function __construct($format)
    {
        parent::__construct("Response format \"$format\" is not supported.");
    }
}

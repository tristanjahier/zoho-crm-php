<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class InvalidHttpMethodException extends Exception
{
    /**
     * The constructor.
     *
     * @param string $method The invalid HTTP method
     */
    public function __construct(string $method)
    {
        parent::__construct("\"$method\" is not a valid HTTP method.");
    }
}

<?php

declare(strict_types=1);

namespace Zoho\Crm\Exceptions;

class InvalidHttpMethodException extends Exception
{
    /**
     * The constructor.
     *
     * @param string $method The invalid HTTP method
     */
    public function __construct(string $method)
    {
        parent::__construct("\"{$method}\" is not a valid HTTP method.");
    }
}

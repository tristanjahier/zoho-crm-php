<?php

namespace Zoho\Crm\Exceptions;

class UnreadableResponseException extends Exception
{
    /** @var string The exception message */
    protected $message = 'Response cannot be read and parsed properly.';
}

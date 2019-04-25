<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class NullAuthTokenException extends Exception
{
    /** @var string The exception message */
    protected $message = 'Invalid auth token: it must not be null or empty.';
}

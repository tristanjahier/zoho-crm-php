<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class InvalidEndpointException extends Exception
{
    protected $message = 'Invalid API endpoint: it must not be null or empty.';
}

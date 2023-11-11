<?php

declare(strict_types=1);

namespace Zoho\Crm\Exceptions;

class InvalidEndpointException extends Exception
{
    /** @var string The exception message */
    protected $message = 'Invalid API endpoint: it must not be null or empty.';
}

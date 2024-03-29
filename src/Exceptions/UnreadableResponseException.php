<?php

declare(strict_types=1);

namespace Zoho\Crm\Exceptions;

class UnreadableResponseException extends Exception
{
    protected $message = 'Response cannot be read and parsed properly.';
}

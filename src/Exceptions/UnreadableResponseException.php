<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class UnreadableResponseException extends Exception
{
    protected $message = 'Response cannot be read and parsed properly.';
}

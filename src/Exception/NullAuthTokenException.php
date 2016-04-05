<?php

namespace Zoho\CRM\Exception;

class NullAuthTokenException extends \Exception
{
    protected $message = 'Invalid auth token: it must not be null or empty.';
}

<?php

namespace Zoho\CRM\Exception;

class UnreadableResponseException extends \Exception
{
    protected $message = 'Response cannot be read and parsed properly.';
}

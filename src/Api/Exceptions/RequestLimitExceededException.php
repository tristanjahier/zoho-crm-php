<?php

namespace Zoho\Crm\Api\Exceptions;

class RequestLimitExceededException extends AbstractException
{
    protected $description = 'Number of API calls exceeded.';

    public function __construct($message)
    {
        parent::__construct($message, '4421');
    }
}

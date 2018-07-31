<?php

namespace Zoho\Crm\Api\Exceptions;

class RecordNotFoundException extends AbstractException
{
    protected $description = 'No record available with the specified record ID.';

    public function __construct($message)
    {
        parent::__construct($message, '4103');
    }
}

<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class InvalidComparisonOperatorException extends Exception
{
    public function __construct($operator)
    {
        parent::__construct("Operator $operator is not a valid comparison operator.");
    }
}

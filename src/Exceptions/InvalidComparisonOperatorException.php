<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class InvalidComparisonOperatorException extends Exception
{
    /**
     * The constructor.
     *
     * @param string $operator The invalid comparison operator
     */
    public function __construct($operator)
    {
        parent::__construct("Operator $operator is not a valid comparison operator.");
    }
}

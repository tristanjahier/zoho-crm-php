<?php

namespace Zoho\Crm\Api\Exceptions;

use Exception;

abstract class AbstractException extends Exception
{
    protected $description = '';

    public function getGenericDescription()
    {
        return $this->description;
    }
}

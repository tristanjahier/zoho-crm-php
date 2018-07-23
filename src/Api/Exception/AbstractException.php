<?php

namespace Zoho\Crm\Api\Exception;

abstract class AbstractException extends \Exception
{
    protected $description = '';

    public function getGenericDescription()
    {
        return $this->description;
    }
}

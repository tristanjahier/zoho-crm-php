<?php

namespace Zoho\CRM\Exception\Api;

abstract class AbstractException extends \Exception
{
    protected $description = '';

    public function getGenericDescription()
    {
        return $this->description;
    }
}

<?php

namespace Zoho\Crm\V1\Exceptions;

use Exception;
use Zoho\Crm\V1\Methods\MethodInterface;

class InvalidMethodHandlerException extends Exception
{
    /**
     * The constructor.
     *
     * @param string $handler The method handler class name
     */
    public function __construct($handler)
    {
        parent::__construct("Class $handler does not exist or does not implement " . MethodInterface::class . '.');
    }
}

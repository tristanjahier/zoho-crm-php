<?php

namespace Zoho\Crm\Api\Exceptions;

use Exception;

/**
 * Base class of the API error exceptions.
 */
abstract class AbstractException extends Exception
{
    /** @var string A generic description of the API error */
    protected $description = '';

    /**
     * Get the generic description of the API error.
     *
     * @return string
     */
    public function getGenericDescription()
    {
        return $this->description;
    }
}

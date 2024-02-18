<?php

declare(strict_types=1);

namespace Zoho\Crm\Exceptions;

use RuntimeException;

/**
 * Base class for all exceptions of the library.
 */
abstract class Exception extends RuntimeException
{
    /**
     * The exception message
     *
     * @var string
     */
    protected $message;
}

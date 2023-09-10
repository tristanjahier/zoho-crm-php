<?php

namespace Zoho\Crm\Exceptions;

class ImmutableEntityException extends Exception
{
    /** @var string The exception message */
    protected $message = 'Immutable entity: cannot modify its attributes.';
}

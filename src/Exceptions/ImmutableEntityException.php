<?php

declare(strict_types=1);

namespace Zoho\Crm\Exceptions;

class ImmutableEntityException extends Exception
{
    /** @var string The exception message */
    protected string $message = 'Immutable entity: cannot modify its attributes.';
}

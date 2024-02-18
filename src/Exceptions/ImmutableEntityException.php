<?php

declare(strict_types=1);

namespace Zoho\Crm\Exceptions;

class ImmutableEntityException extends Exception
{
    protected $message = 'Immutable entity: cannot modify its attributes.';
}

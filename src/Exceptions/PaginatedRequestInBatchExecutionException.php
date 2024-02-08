<?php

declare(strict_types=1);

namespace Zoho\Crm\Exceptions;

class PaginatedRequestInBatchExecutionException extends Exception
{
    /** @var string The exception message */
    protected $message = 'Paginated requests cannot be sent inside a batch.';
}

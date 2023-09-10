<?php

namespace Zoho\Crm\Exceptions;

class PaginatedQueryInBatchExecutionException extends Exception
{
    /** @var string The exception message */
    protected $message = 'Paginated queries cannot be sent inside a batch.';
}

<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class PaginatedQueryInBatchExecutionException extends Exception
{
    /** @var string The exception message */
    protected $message = 'Paginated queries cannot be sent inside a batch.';
}

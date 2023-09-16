<?php

namespace Zoho\Crm\Exceptions;

class PaginatedRequestInBatchExecutionException extends Exception
{
    /** @var string The exception message */
    protected $message = 'Paginated requests cannot be sent inside a batch.';
}

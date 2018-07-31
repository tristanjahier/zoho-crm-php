<?php

namespace Zoho\Crm\Exception;

use Exception;
use Zoho\Crm\Api\Query;

class InvalidQueryException extends Exception
{
    public function __construct(Query $query, $message)
    {
        parent::__construct("Invalid query: $message (URI: {$query->buildUri()})");
    }
}

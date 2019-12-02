<?php

namespace Zoho\Crm\Exceptions;

use Exception;
use Zoho\Crm\Contracts\QueryInterface;

class InvalidQueryException extends Exception
{
    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Contracts\QueryInterface $query The API query
     * @param string $message A short message explaining why the query is invalid
     */
    public function __construct(QueryInterface $query, $message)
    {
        parent::__construct("Invalid query: $message (URI: {$query->getUri()})");
    }
}

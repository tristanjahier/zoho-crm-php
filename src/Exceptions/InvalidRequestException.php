<?php

namespace Zoho\Crm\Exceptions;

use Zoho\Crm\Contracts\RequestInterface;

class InvalidRequestException extends Exception
{
    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Contracts\RequestInterface $request The API request
     * @param string $message A short message explaining why the request is invalid
     */
    public function __construct(RequestInterface $request, $message)
    {
        parent::__construct("Invalid request: $message ({$request->getHttpMethod()} /{$request->getUrl()})");
    }
}

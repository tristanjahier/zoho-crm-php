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
        $url = '/' . $request->getUrlPath();

        if ($request->getUrlParameters()->isNotEmpty()) {
            $url .= '?' . $request->getUrlParameters();
        }

        parent::__construct("Invalid request: {$message} ({$request->getHttpMethod()} {$url})");
    }
}

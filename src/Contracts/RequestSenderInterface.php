<?php

namespace Zoho\Crm\Contracts;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestSenderInterface
{
    /**
     * Send an HTTP request to the API, and return the response.
     *
     * @param \Psr\Http\Message\RequestInterface $request The request to send
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function send(RequestInterface $request): ResponseInterface;

    /**
     * Prepare an asynchronous HTTP request to the API, and return a promise.
     *
     * @param \Psr\Http\Message\RequestInterface $request The request to send
     * @param \Closure $onFulfilled The closure to handle request success
     * @param \Closure|null $onRejected (optional) The closure to handle request failure
     */
    public function sendAsync(RequestInterface $request, Closure $onFulfilled, Closure $onRejected = null);

    /**
     * Settle a batch of HTTP promises, then return all responses.
     *
     * @param array $promises The promises to settle
     * @return \Psr\Http\Message\ResponseInterface[]
     */
    public function fetchAsyncResponses(array $promises);

    /**
     * Get the number of API requests sent so far.
     *
     * @return int
     */
    public function getRequestCount(): int;
}

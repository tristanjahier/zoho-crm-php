<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

use Http\Promise\Promise as PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpLayerInterface
{
    /**
     * Create an HTTP request object with the given components.
     *
     * @param string $method The HTTP method (GET, POST etc.)
     * @param string $url The full URL
     * @param array $headers The request headers
     * @param string $body The request body
     * @return \Psr\Http\Message\RequestInterface
     */
    public function createRequest(string $method, string $url, array $headers, string $body): RequestInterface;

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
     * @param callable $onFulfilled The closure to handle request success
     * @param callable|null $onRejected (optional) The closure to handle request failure
     * @return \Http\Promise\Promise
     */
    public function sendAsync(RequestInterface $request, callable $onFulfilled, callable $onRejected = null): PromiseInterface;

    /**
     * Settle a batch of HTTP promises, then return all responses.
     *
     * @param array $promises The promises to settle
     * @return \Psr\Http\Message\ResponseInterface[]
     */
    public function fetchAsyncResponses(array $promises): array;

    /**
     * Get the number of API requests sent so far.
     *
     * @return int
     */
    public function getRequestCount(): int;
}
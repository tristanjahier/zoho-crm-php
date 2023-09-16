<?php

namespace Zoho\Crm\Contracts;

use Closure;

interface ClientInterface
{
    /**
     * Set the API endpoint base URL.
     *
     * It will ensure that there is one slash at the end.
     *
     * @param string $endpoint The endpoint base URL
     * @return void
     *
     * @throws \Zoho\Crm\Exceptions\InvalidEndpointException
     */
    public function setEndpoint(string $endpoint): void;

    /**
     * Get the API endpoint base URL.
     *
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * Execute a request and get a formal and generic response object.
     *
     * @param RequestInterface $request The request to execute
     * @return ResponseInterface
     */
    public function executeRequest(RequestInterface $request): ResponseInterface;

    /**
     * Execute a batch of requests concurrently and get the responses when all received.
     *
     * @param RequestInterface[] $requests The batch of requests to execute
     * @return ResponseInterface[]
     */
    public function executeAsyncBatch(array $requests): array;

    /**
     * Register a callback to execute before each request.
     *
     * @param \Closure $callback The callback to execute
     * @return self
     */
    public function beforeRequestExecution(Closure $callback): self;

    /**
     * Register a callback to execute after each request.
     *
     * @param \Closure $callback The callback to execute
     * @return self
     */
    public function afterRequestExecution(Closure $callback): self;

    /**
     * Get the number of API requests made by the client.
     *
     * @return int
     */
    public function getRequestCount(): int;

    /**
     * Register a middleware that will be applied to each request before execution.
     *
     * The request may be altered by the middleware.
     *
     * @param callable $middleware The middleware to register
     * @return void
     */
    public function registerMiddleware(callable $middleware): void;
}

<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

interface ClientInterface
{
    /**
     * Set the API endpoint base URL.
     *
     * It will ensure that there is one slash at the end.
     *
     * @param string $endpoint The endpoint base URL
     *
     * @throws \Zoho\Crm\Exceptions\InvalidEndpointException
     */
    public function setEndpoint(string $endpoint);

    /**
     * Get the API endpoint base URL.
     */
    public function getEndpoint(): string;

    /**
     * Execute a request and get a formal and generic response object.
     *
     * @param RequestInterface $request The request to execute
     */
    public function executeRequest(RequestInterface $request): ResponseInterface;

    /**
     * Execute a batch of asynchronous requests concurrently and return the responses when all received.
     *
     * @param RequestInterface[] $requests The batch of requests to execute
     * @return ResponseInterface[]
     */
    public function executeAsyncRequestBatch(array $requests): array;

    /**
     * Register a callback to execute before each request.
     *
     * @param callable $callback The callback to execute
     * @param string $id (optional) A unique identifier for the callback
     * @param bool $overwrite (optional) Whether to replace an existing callback having the same identifier
     *
     * @throws \InvalidArgumentException When the identifier is invalid
     * @throws \RuntimeException When the identifier is already taken
     */
    public function beforeEachRequest(callable $callback, string $id = null, bool $overwrite = false);

    /**
     * Register a callback to execute after each request.
     *
     * @param callable $callback The callback to execute
     * @param string $id (optional) A unique identifier for the callback
     * @param bool $overwrite (optional) Whether to replace an existing callback having the same identifier
     *
     * @throws \InvalidArgumentException When the identifier is invalid
     * @throws \RuntimeException When the identifier is already taken
     */
    public function afterEachRequest(callable $callback, string $id = null, bool $overwrite = false);

    /**
     * Remove an identified callback that was to execute before each request.
     *
     * @param string $id The unique identifier of the callback
     *
     * @throws \InvalidArgumentException When the identifier is invalid
     * @throws \RuntimeException When there is no callback with this identifier
     */
    public function cancelBeforeEachRequestCallback(string $id);

    /**
     * Remove an identified callback that was to execute after each request.
     *
     * @param string $id The unique identifier of the callback
     *
     * @throws \InvalidArgumentException When the identifier is invalid
     * @throws \RuntimeException When there is no callback with this identifier
     */
    public function cancelAfterEachRequestCallback(string $id);

    /**
     * Get the number of API requests made by the client.
     */
    public function getRequestCount(): int;

    /**
     * Register a middleware that will be applied to each request before execution.
     *
     * The request may be altered by the middleware.
     *
     * @param callable $middleware The middleware to register
     */
    public function registerMiddleware(callable $middleware);

    /**
     * Get the client preferences container.
     */
    public function preferences(): ClientPreferenceContainerInterface;
}

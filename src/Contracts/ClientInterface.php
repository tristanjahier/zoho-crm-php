<?php

namespace Zoho\Crm\Contracts;

use Closure;

interface ClientInterface
{
    /**
     * Set the API endpoint.
     *
     * It will ensure that there is one slash at the end.
     *
     * @param string $endpoint The endpoint URI
     * @return void
     *
     * @throws \Zoho\Crm\Exceptions\InvalidEndpointException
     */
    public function setEndpoint(string $endpoint): void;

    /**
     * Get the API endpoint.
     *
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * Execute a query and get a formal and generic response object.
     *
     * @param QueryInterface $query The query to execute
     * @return ResponseInterface
     */
    public function executeQuery(QueryInterface $query): ResponseInterface;

    /**
     * Execute a batch of queries concurrently and get the responses when all received.
     *
     * @param QueryInterface[] $queries The batch of queries to execute
     * @return ResponseInterface[]
     */
    public function executeAsyncBatch(array $queries): array;

    /**
     * Register a callback to execute before each query execution.
     *
     * @param \Closure $callback The callback to execute
     * @return self
     */
    public function beforeQueryExecution(Closure $callback): self;

    /**
     * Register a callback to execute after each query execution.
     *
     * @param \Closure $callback The callback to execute
     * @return self
     */
    public function afterQueryExecution(Closure $callback): self;

    /**
     * Get the number of API requests made by the client.
     *
     * @return int
     */
    public function getRequestCount(): int;

    /**
     * Register a middleware that will be applied to each query before execution.
     *
     * The query may be altered by the middleware.
     *
     * @param callable $middleware The middleware to register
     * @return void
     */
    public function registerMiddleware(callable $middleware): void;
}

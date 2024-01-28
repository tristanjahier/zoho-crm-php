<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

interface RequestInterface extends HttpRequestableInterface
{
    /**
     * Validate the request.
     *
     * Check attributes consistency and the presence of the required ones.
     * If the validation fails an exception will be thrown.
     *
     * @throws \Zoho\Crm\Exceptions\InvalidRequestException
     */
    public function validate();

    /**
     * Create a deep copy of the request.
     *
     * @return static
     */
    public function copy(): static;

    /**
     * Get the bound API client.
     *
     * @return ClientInterface
     */
    public function getClient(): ClientInterface;

    /**
     * Execute the request with the bound client.
     *
     * @return ResponseInterface
     */
    public function execute(): ResponseInterface;

    /**
     * Execute the request and get a result adapted to its nature.
     *
     * @return mixed
     */
    public function get(): mixed;

    /**
     * Get an instance of a response transformer.
     *
     * It may return null if there is no need of additional response transformation.
     *
     * @return ResponseTransformerInterface|null
     */
    public function getResponseTransformer(): ?ResponseTransformerInterface;
}

<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

interface RequestInterface extends HttpRequestableInterface
{
    /**
     * Validate the request.
     *
     * Check attributes consistency and the presence of the required ones.
     * If the validation passes, nothing will happen.
     * If it fails, an exception will be thrown.
     *
     * @return void
     *
     * @throws \Zoho\Crm\Exceptions\InvalidRequestException
     */
    public function validate(): void;

    /**
     * Create a deep copy of the request.
     *
     * @return self
     */
    public function copy(): self;

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
    public function get();

    /**
     * Get an instance of a response transformer.
     *
     * It may return null if there is no need of additional response transformation.
     *
     * @return ResponseTransformerInterface|null
     */
    public function getResponseTransformer(): ?ResponseTransformerInterface;
}

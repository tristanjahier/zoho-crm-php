<?php

namespace Zoho\Crm\Contracts;

interface QueryInterface extends RequestableInterface
{
    /**
     * Validate the query.
     *
     * Check attributes consistency and the presence of the required ones.
     * If the validation passes, nothing will happen.
     * If it fails, an exception will be thrown.
     *
     * @return void
     *
     * @throws \Zoho\Crm\Exceptions\InvalidQueryException
     */
    public function validate(): void;

    /**
     * Create a deep copy of the query.
     *
     * @return self
     */
    public function copy(): self;

    /**
     * Execute the query with the bound client.
     *
     * @return ResponseInterface
     */
    public function execute(): ResponseInterface;

    /**
     * Execute the query and get a result adapted to its nature.
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

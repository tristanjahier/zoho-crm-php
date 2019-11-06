<?php

namespace Zoho\Crm\Contracts;

interface QueryInterface
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
     * Build the query URI.
     *
     * @return string
     */
    public function buildUri(): string;

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
}

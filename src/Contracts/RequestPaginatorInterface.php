<?php

namespace Zoho\Crm\Contracts;

interface RequestPaginatorInterface
{
    /**
     * Fetch pages until there is no more data to fetch.
     */
    public function fetchAll();

    /**
     * Get all fetched responses.
     *
     * @return ResponseInterface[]
     */
    public function getResponses(): array;
}

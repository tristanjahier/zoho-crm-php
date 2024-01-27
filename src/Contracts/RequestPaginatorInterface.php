<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

interface RequestPaginatorInterface
{
    /**
     * Get a request for the next page to fetch, and move forward the page cursor.
     */
    public function getNextPageRequest(): RequestInterface;

    /**
     * Handle a freshly retrieved page, perform checks, alter contents if needed.
     */
    public function handlePage(ResponseInterface $pageResponse): void;

    /**
     * Determine if there could be more data to fetch.
     */
    public function hasMoreData(): bool;
}

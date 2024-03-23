<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

interface PaginatedRequestInterface extends RequestInterface
{
    /**
     * Determine if the request must be automatically paginated.
     */
    public function mustBePaginatedAutomatically(): bool;

    /**
     * Set the request not to be automatically paginated.
     */
    public function disableAutomaticPagination();

    /**
     * Determine if the request requires concurrent and asynchronous pagination.
     */
    public function mustBePaginatedConcurrently(): bool;

    /**
     * Get the concurrency limit for asynchronous pagination.
     */
    public function getConcurrency(): ?int;

    /**
     * Create a paginator for the request.
     */
    public function getPaginator(): RequestPaginatorInterface;

    /**
     * Get a page content merger for paginated requests.
     */
    public function getResponsePageMerger(): ResponsePageMergerInterface;
}

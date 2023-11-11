<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

interface PaginatedRequestInterface extends RequestInterface
{
    /**
     * Determine if the request must be automatically paginated.
     *
     * @return bool
     */
    public function mustBePaginatedAutomatically(): bool;

    /**
     * Determine if the request requires concurrent and asynchronous pagination.
     *
     * @return bool
     */
    public function mustBePaginatedConcurrently(): bool;

    /**
     * Get the concurrency limit for asynchronous pagination.
     *
     * @return int|null
     */
    public function getConcurrency(): ?int;

    /**
     * Create a paginator for the request.
     *
     * @return RequestPaginatorInterface
     */
    public function getPaginator(): RequestPaginatorInterface;

    /**
     * Get a page content merger for paginated requests.
     *
     * @return ResponsePageMergerInterface
     */
    public function getResponsePageMerger(): ResponsePageMergerInterface;
}

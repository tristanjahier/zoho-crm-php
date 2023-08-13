<?php

namespace Zoho\Crm\Contracts;

interface PaginatedQueryInterface extends QueryInterface
{
    /**
     * Determine if the query must be automatically paginated.
     *
     * @return bool
     */
    public function mustBePaginatedAutomatically(): bool;

    /**
     * Determine if the query requires concurrent and asynchronous pagination.
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
     * Create a paginator for the query.
     *
     * @return QueryPaginatorInterface
     */
    public function getPaginator(): QueryPaginatorInterface;

    /**
     * Get a page content merger for paginated queries.
     *
     * @return ResponsePageMergerInterface
     */
    public function getResponsePageMerger(): ResponsePageMergerInterface;
}

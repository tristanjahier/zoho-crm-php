<?php

namespace Zoho\Crm\Contracts;

use Zoho\Crm\Api\QueryPaginator;

interface PaginatedQueryInterface extends QueryInterface
{
    /**
     * Turn pagination on/off for the query.
     *
     * If enabled, the pages will be automatically fetched on query execution.
     *
     * @param bool $paginated (optional) Whether the query is paginated
     * @return self
     */
    public function paginated(bool $paginated = true): self;

    /**
     * Check if the query is paginated.
     *
     * @return bool
     */
    public function isPaginated(): bool;

    /**
     * Create a paginator for the query.
     *
     * @return \Zoho\Crm\Api\QueryPaginator
     */
    public function getPaginator(): QueryPaginator;
}

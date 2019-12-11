<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\{
    PaginatedQueryInterface,
    QueryPaginatorInterface,
    ResponsePageMergerInterface
};
use Zoho\Crm\Traits\HasPagination;
use Zoho\Crm\V2\QueryPaginator;
use Zoho\Crm\V2\CollectionPageMerger;

/**
 * Base class for Record APIs paginated queries.
 */
abstract class AbstractPaginatedQuery extends AbstractQuery implements PaginatedQueryInterface
{
    use HasPagination;

    /**
     * @inheritdoc
     *
     * @return \Zoho\Crm\V2\QueryPaginator
     */
    public function getPaginator(): QueryPaginatorInterface
    {
        return new QueryPaginator($this);
    }

    /**
     * @inheritdoc
     *
     * @return \Zoho\Crm\V2\CollectionPageMerger
     */
    public function getResponsePageMerger(): ResponsePageMergerInterface
    {
        return new CollectionPageMerger();
    }

    /**
     * Set the page of records to retrieve.
     *
     * @param int $page The page number
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function page(int $page)
    {
        if ($page <= 0) {
            throw new \InvalidArgumentException('Page number must be a positive non-zero integer.');
        }

        return $this->param('page', $page);
    }

    /**
     * Set the number of records to get per page.
     *
     * @param int $perPage The number of records
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function perPage(int $perPage)
    {
        $max = QueryPaginator::PAGE_MAX_SIZE;

        if ($perPage <= 0 || $perPage > $max) {
            throw new \InvalidArgumentException("\"Per page\" number must be between 1 and $max.");
        }

        return $this->param('per_page', $perPage);
    }
}

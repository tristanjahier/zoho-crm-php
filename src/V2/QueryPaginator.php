<?php

namespace Zoho\Crm\V2;

use Zoho\Crm\AbstractQueryPaginator;
use Zoho\Crm\Contracts\QueryInterface;

/**
 * Paginator for API v2 queries.
 */
class QueryPaginator extends AbstractQueryPaginator
{
    /** @var int The latest page fetched */
    protected $latestPageFetched = 0;

    /**
     * @inheritdoc
     *
     * @return AbstractQuery
     */
    protected function getNextPageQuery(): QueryInterface
    {
        return $this->query->copy()
            ->autoPaginated(false)
            ->param('page', ++$this->latestPageFetched);
    }

    /**
     * @inheritdoc
     */
    protected function getPageSize(): int
    {
        return (int) ($this->query->getUrlParameter('per_page') ?? static::PAGE_MAX_SIZE);
    }
}

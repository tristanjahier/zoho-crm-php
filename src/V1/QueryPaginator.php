<?php

namespace Zoho\Crm\V1;

use Zoho\Crm\AbstractQueryPaginator;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Entities\Collection;
use Zoho\Crm\Response;

/**
 * Paginator for API v1 queries.
 */
class QueryPaginator extends AbstractQueryPaginator
{
    /** @var int The index of the first item */
    const MIN_INDEX = 1;

    /** @var int The last fetched index */
    private $lastFetchedIndex = 0;

    /**
     * @inheritdoc
     *
     * @return Query
     */
    protected function getNextPageQuery(): QueryInterface
    {
        $query = $this->query->copy()
            ->autoPaginated(false)
            ->param('fromIndex', $this->lastFetchedIndex + 1)
            ->param('toIndex', $this->lastFetchedIndex + self::PAGE_MAX_SIZE);

        // Move the record index pointer forward
        $this->lastFetchedIndex += self::PAGE_MAX_SIZE;

        return $query;
    }

    /**
     * @inheritdoc
     */
    protected function getPageSize(): int
    {
        return static::PAGE_MAX_SIZE;
    }

    /**
     * @inheritdoc
     */
    protected function handlePage(Response &$page)
    {
        parent::handlePage($page);

        // Apply the limit of records to be fetched
        if ($this->query->hasLimit()) {
            $limit = $this->query->getLimit();
            $recordsFetched = $this->getNumberOfItemsFetched();

            if ($recordsFetched >= $limit) {
                $this->hasMoreData = false;
                $diff = $recordsFetched - $limit;
                $count = count($page->getContent());

                // Get rid of the extra items
                $page->setContent(
                    $this->limitPageContents($page->getContent(), $count - $diff)
                );
            }
        }

        // Apply the limit of the modification date
        if ($this->query->hasMaxModificationDate() && ! $page->isEmpty()) {
            $records = $page->getContent();

            if ($this->exceedMaxModifiedTime($records)) {
                $this->hasMoreData = false;
                $page->setContent($this->purgeRecordsExceedingMaxModifiedTime($records));
            }
        }
    }

    /**
     * Take only the first given number of items from a page of results.
     *
     * @param mixed $contents The page of results
     * @param int $limit The number of items to keep
     * @return mixed
     */
    private function limitPageContents($contents, int $limit)
    {
        if ($this->query->getMethod() == 'getDeletedRecordIds') {
            return array_slice($contents, 0, $limit);
        }

        return $contents->slice(0, $limit);
    }

    /**
     * Check if the last record of a page exceeds the maximum modification date.
     *
     * @param \Zoho\Crm\Entities\Collection $records The page of records to check
     * @return bool
     */
    private function exceedMaxModifiedTime(Collection $records)
    {
        $modifiedAt = new \DateTime($records->last()->get('Modified Time'));

        return $modifiedAt >= $this->query->getMaxModificationDate();
    }

    /**
     * Remove all records from a page whose last modification date exceeds
     * the maximum date set on the query.
     *
     * @param \Zoho\Crm\Entities\Collection $records The page of records to filter
     * @return \Zoho\Crm\Entities\Collection
     */
    private function purgeRecordsExceedingMaxModifiedTime(Collection $records)
    {
        return $records->filter(function ($record) {
            $modifiedAt = new \DateTime($record->get('Modified Time'));
            return $modifiedAt < $this->query->getMaxModificationDate();
        });
    }
}

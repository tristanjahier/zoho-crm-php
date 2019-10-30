<?php

namespace Zoho\Crm\Api;

use DateTime;
use Zoho\Crm\Entities\Collection;

/**
 * A helper class to handle paginated queries.
 */
class QueryPaginator
{
    /** @var int The index of the first item */
    const MIN_INDEX = 1;

    /** @var int The maximum number of items per page */
    const PAGE_MAX_SIZE = 200;

    /** @var Query The parent query */
    private $query;

    /** @var int The last fetched index */
    private $lastFetchedIndex = 0;

    /** @var bool Whether there is still data to fetch */
    private $hasMoreData = true;

    /** @var int The number of pages fetched */
    private $fetchCount = 0;

    /** @var Response[] The responses that have been retrieved for each page */
    private $responses = [];

    /**
     * The constructor.
     *
     * @param Query $query The parent query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Get the parent query.
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get all page responses.
     *
     * @return Response[]
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * Get the number of pages fetched.
     *
     * @return int
     */
    public function getNumberOfPagesFetched()
    {
        return $this->fetchCount;
    }

    /**
     * Get the number of records fetched.
     *
     * @return int
     */
    public function getNumberOfRecordsFetched()
    {
        return array_reduce($this->responses, function ($sum, $response) {
            return $sum + count($response->getContent());
        }, 0);
    }

    /**
     * Check if there is more data to fetch.
     *
     * There is no actual check, so if it returns true, it only means
     * that as far as we know, we have not fetched the last record/page yet.
     * The value is updated after each fetch.
     *
     * @return bool
     */
    public function hasMoreData()
    {
        return $this->hasMoreData;
    }

    /**
     * Fetch a new page.
     *
     * It creates a copy of the parent query, and changes the page indexes
     * to match the current state of fetching.
     *
     * @return Response|null
     */
    public function fetch()
    {
        if (! $this->hasMoreData) {
            return;
        }

        $pageResponse = $this->getNextPageQuery()->execute();
        $this->handlePageResponse($pageResponse);
        $this->fetchCount++;

        return $pageResponse;
    }

    /**
     * Get a query for the next page to fetch, and move forward the page cursor.
     *
     * @return Query
     */
    private function getNextPageQuery()
    {
        $query = $this->query->copy()
            ->paginated(false)
            ->param('fromIndex', $this->lastFetchedIndex + 1)
            ->param('toIndex', $this->lastFetchedIndex + self::PAGE_MAX_SIZE);

        // Move the record index pointer forward
        $this->lastFetchedIndex += self::PAGE_MAX_SIZE;

        return $query;
    }

    /**
     * Handle a freshly retrieved page, perform checks, alter contents if needed.
     *
     * @param Response $pageResponse The page response
     * @return void
     */
    private function handlePageResponse(Response $pageResponse)
    {
        // If this page is empty, then the following ones will be too
        if ($pageResponse->isEmpty()) {
            $this->hasMoreData = false;
            return;
        }

        $this->responses[] = $pageResponse;

        // The query can carry additional constraints that we need to process
        $this->applyQueryConstraints($pageResponse);
    }

    /**
     * Fetch pages until there is no more data to fetch.
     *
     * @return Response[]
     */
    public function fetchAll()
    {
        if ($this->query->mustFetchPagesAsynchronously()) {
            return $this->fetchAllAsync();
        }

        return $this->fetchAllSync();
    }

    /**
     * Fetch pages synchronously until there is no more data to fetch.
     *
     * @return Response[]
     */
    public function fetchAllSync()
    {
        while ($this->hasMoreData) {
            $this->fetch();
        }

        return $this->responses;
    }

    /**
     * Fetch pages asynchronously by batches until there is no more data to fetch.
     *
     * @param int|null $concurrency (optional) The concurrency limit override value
     * @return Response[]
     */
    public function fetchAllAsync(int $concurrency = null)
    {
        while ($this->hasMoreData) {
            $this->fetchConcurrently($concurrency ?? $this->query->getConcurrency());
        }

        return $this->responses;
    }

    /**
     * Fetch a given maximum number of pages.
     *
     * The limit is global, it is not only bound to one execution of the method,
     * since it is based on the $fetchCount instance property.
     *
     * @param int $limit The maximum number of pages to fetch
     * @return Response[]
     */
    public function fetchLimit($limit)
    {
        while ($this->hasMoreData && $this->fetchCount < $limit) {
            $this->fetch();
        }

        return $this->responses;
    }

    /**
     * Fetch a given number of pages concurrently.
     *
     * @param int $concurrentQueries The number of pages to fetch
     * @return Response[]
     */
    public function fetchConcurrently(int $concurrentQueries)
    {
        if (! $this->hasMoreData) {
            return;
        }

        $queries = [];

        for ($i = 0; $i < $concurrentQueries; $i++) {
            $queries[] = $this->getNextPageQuery();
        }

        $responses = $this->query->getClient()->executeAsyncBatch($queries);

        foreach ($responses as $pageResponse) {
            if (! $this->hasMoreData) {
                break;
            }

            $this->handlePageResponse($pageResponse);
        }

        $this->fetchCount += $concurrentQueries;

        return $this->responses;
    }

    /**
     * Apply the constraints of the parent query to a response.
     *
     * @param Response $latestResponse The latest response fetched
     * @return void
     */
    private function applyQueryConstraints(Response $latestResponse)
    {
        // Apply the limit of records to be fetched
        if ($this->query->hasLimit()) {
            $limit = $this->query->getLimit();
            $recordsFetched = $this->getNumberOfRecordsFetched();

            if ($recordsFetched > $limit) {
                $this->hasMoreData = false;
                $diff = $recordsFetched - $limit;
                $count = count($latestResponse->getContent());

                // Get rid of the extra items
                $latestResponse->setContent(
                    $this->limitPageContents($latestResponse->getContent(), $count - $diff)
                );
            }
        }

        // Apply the limit of the modification date
        if ($this->query->hasMaxModificationDate() && ! $latestResponse->isEmpty()) {
            $records = $latestResponse->getContent();

            if ($this->exceedMaxModifiedTime($records)) {
                $this->hasMoreData = false;
                $latestResponse->setContent($this->purgeRecordsExceedingMaxModifiedTime($records));
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
        $modifiedAt = new DateTime($records->last()->get('Modified Time'));

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
            $modifiedAt = new DateTime($record->get('Modified Time'));
            return $modifiedAt < $this->query->getMaxModificationDate();
        });
    }
}

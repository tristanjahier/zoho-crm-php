<?php

namespace Zoho\Crm\Api;

use DateTime;

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
    private $last_fetched_index = 0;

    /** @var bool Whether there is still data to fetch */
    private $has_more_data = true;

    /** @var int The number of pages fetched */
    private $fetch_count = 0;

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
        return $this->fetch_count;
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
        return $this->has_more_data;
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
        if (! $this->has_more_data) {
            return;
        }

        // Create a temporary query object with pagination parameters
        $page_query = $this->query->copy()
            ->paginated(false)
            ->param('fromIndex', $this->last_fetched_index + 1)
            ->param('toIndex', $this->last_fetched_index + self::PAGE_MAX_SIZE);

        $page_response = $page_query->execute();

        $this->responses[] = $page_response;

        // Determine if there is more data to fetch
        if ($page_response->isEmpty()) {
            $this->has_more_data = false;
        } else {
            // The query can carry additional constraints that we need to process
            $this->applyQueryConstraints($page_response);
        }

        // Move the record index pointer forward
        $this->last_fetched_index += self::PAGE_MAX_SIZE;

        $this->fetch_count++;

        return $page_response;
    }

    /**
     * Fetch pages until there is no more data to fetch.
     *
     * @return Response[]
     */
    public function fetchAll()
    {
        while ($this->has_more_data) {
            $this->fetch();
        }

        return $this->responses;
    }

    /**
     * Fetch a given maximum number of pages.
     *
     * The limit is global, it is not only bound to one execution of the method,
     * since it is based on the $fetch_count instance property.
     *
     * @param int $limit The maximum number of pages to fetch
     * @return Response[]
     */
    public function fetchLimit($limit)
    {
        while ($this->has_more_data && $this->fetch_count < $limit) {
            $this->fetch();
        }

        return $this->responses;
    }

    /**
     * Apply the constraints of the parent query to a response.
     *
     * @param Response $latest_response The latest response fetched
     * @return void
     */
    private function applyQueryConstraints(Response $latest_response)
    {
        // Apply the limit of records to be fetched
        if ($this->query->hasLimit()) {
            $limit = $this->query->getLimit();
            $records_fetched = $this->getNumberOfRecordsFetched();

            if ($records_fetched > $limit) {
                $this->has_more_data = false;
                $diff = $records_fetched - $limit;
                $count = count($latest_response->getContent());

                // Get rid of the extra records
                $latest_response->setContent(
                    array_slice($latest_response->getContent(), 0, $count - $diff)
                );
            }
        }

        // Apply the limit of the modification date
        if ($this->query->hasMaxModificationDate() && $latest_response->containsRecords()) {
            $records = $latest_response->getContent();

            if ($this->exceedMaxModifiedTime($records)) {
                $this->has_more_data = false;
                $latest_response->setContent($this->purgeRecordsExceedingMaxModifiedTime($records));
            }
        }
    }

    /**
     * Check if the last record of an array exceeds the maximum modification date.
     *
     * @param array $records The array of records to check
     * @return bool
     */
    private function exceedMaxModifiedTime(array $records)
    {
        $last_record = end($records);
        $modified_at = new DateTime($last_record['Modified Time']);

        return $modified_at >= $this->query->getMaxModificationDate();
    }

    /**
     * Remove all records from an array whose last modification date exceeds
     * the maximum date set on the parent query.
     *
     * @param array $records The array of records to filter
     * @return array
     */
    private function purgeRecordsExceedingMaxModifiedTime(array $records)
    {
        return array_filter($records, function ($record) {
            $modified_at = new DateTime($record['Modified Time']);
            return $modified_at < $this->query->getMaxModificationDate();
        });
    }

    /**
     * Aggregate all the fetched contents in one response.
     *
     * @return Response
     */
    public function getAggregatedResponse()
    {
        $content = [];
        $raw_content = [];

        // Extract data from each response
        foreach ($this->responses as $resp) {
            $content[] = $resp->getContent();
            $raw_content[] = $resp->getRawContent();
        }

        // Get rid of potential null data
        $content = array_filter($content);

        // Merge it all
        $content = count($content) > 0 ? array_merge(...$content) : null;

        return new Response($this->query, $content, $raw_content);
    }
}

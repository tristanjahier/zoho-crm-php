<?php

namespace Zoho\Crm\Api;

use DateTime;

class QueryPaginator
{
    const MIN_INDEX = 1;

    const PAGE_MAX_SIZE = 200;

    private $query;

    private $last_fetched_index = 0;

    private $has_more_data = true;

    private $fetch_count = 0;

    private $responses = [];

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getResponses()
    {
        return $this->responses;
    }

    public function getNumberOfPagesFetched()
    {
        return $this->fetch_count;
    }

    public function getNumberOfRecordsFetched()
    {
        return array_reduce($this->responses, function ($sum, $response) {
            return $sum + count($response->getContent());
        }, 0);
    }

    public function hasMoreData()
    {
        return $this->has_more_data;
    }

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

    public function fetchAll()
    {
        while ($this->has_more_data) {
            $this->fetch();
        }

        return $this->responses;
    }

    public function fetchLimit($limit)
    {
        while ($this->has_more_data && $this->fetch_count < $limit) {
            $this->fetch();
        }

        return $this->responses;
    }

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

    private function exceedMaxModifiedTime(array $records)
    {
        $last_record = end($records);
        $modified_at = new DateTime($last_record['Modified Time']);

        return $modified_at >= $this->query->getMaxModificationDate();
    }

    private function purgeRecordsExceedingMaxModifiedTime(array $records)
    {
        return array_filter($records, function ($record) {
            $modified_at = new DateTime($record['Modified Time']);
            return $modified_at < $this->query->getMaxModificationDate();
        });
    }

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
        $content = count($content) > 0 ? call_user_func_array('array_merge', $content) : null;

        return new Response($this->query, $content, $raw_content);
    }
}

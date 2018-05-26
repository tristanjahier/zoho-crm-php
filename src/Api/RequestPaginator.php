<?php

namespace Zoho\CRM\Api;

class RequestPaginator
{
    const MIN_INDEX = 1;

    const PAGE_MAX_SIZE = 200;

    private $request;

    private $last_fetched_index = 0;

    private $has_more_data = true;

    private $fetch_count = 0;

    private $responses = [];

    private $max_modified_time = null;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponses()
    {
        return $this->responses;
    }

    public function getNumberOfPagesFetched()
    {
        return $this->fetch_count;
    }

    public function hasMoreData()
    {
        return $this->has_more_data;
    }

    public function setMaxModifiedTime(\DateTime $date)
    {
        $this->max_modified_time = $date;
    }

    public function fetch()
    {
        if (! $this->has_more_data) {
            return;
        }

        // Create a temporary request object with pagination parameters
        $paginated_request = clone $this->request;
        $paginated_request->setParameters(
            $this->request->getParameters()->extend([
                'fromIndex' => $this->last_fetched_index + 1,
                'toIndex' => $this->last_fetched_index + self::PAGE_MAX_SIZE
            ])
        );

        $raw_data = RequestLauncher::fire($paginated_request);
        $clean_data = ResponseParser::clean($this->request, $raw_data);

        // Determine if there is more data to fetch
        if ($clean_data === null) {
            $this->has_more_data = false;
        } elseif ($this->exceedMaxModifiedTime($clean_data)) {
            // If 'maxModifiedTime' parameter is present, check that we haven't exceeded it yet
            $this->has_more_data = false;
            $clean_data = $this->purgeRecordsExceedingMaxModifiedTime($clean_data);
        }

        $response = new Response($this->request, $raw_data, $clean_data);

        $this->responses[] = $response;

        // Move the record index pointer forward
        $this->last_fetched_index += self::PAGE_MAX_SIZE;

        $this->fetch_count++;

        return $response;
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

    public function getAggregatedResponse()
    {
        $raw_data = [];
        $clean_data = [];

        // Extract data from each response
        foreach ($this->responses as $resp) {
            $raw_data[] = $resp->getRawData();
            $clean_data[] = $resp->getContent();
        }

        // Get rid of potential null data
        $clean_data = array_filter($clean_data);

        // Merge it all
        $clean_data = count($clean_data) > 0 ? call_user_func_array('array_merge', $clean_data) : null;

        return new Response($this->request, $raw_data, $clean_data);
    }

    private function exceedMaxModifiedTime(array $response)
    {
        if ($this->max_modified_time === null) {
            return false;
        }

        $last_record = $response[count($response) - 1];
        $modified_at = new \DateTime($last_record['Modified Time']);

        return $modified_at >= $this->max_modified_time;
    }

    private function purgeRecordsExceedingMaxModifiedTime(array $records)
    {
        return array_filter($records, function ($record) {
            $modified_at = new \DateTime($record['Modified Time']);
            return $modified_at < $this->max_modified_time;
        });
    }
}

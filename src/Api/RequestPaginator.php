<?php

namespace Zoho\CRM\Api;

class RequestPaginator
{
    const MIN_INDEX = 1;

    const PAGE_MAX_SIZE = 200;

    private $request;

    private $last_fetched_index = 0;

    private $has_more_data = true;

    private $responses = [];

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

    public function hasMoreData()
    {
        return $this->has_more_data;
    }

    public function fetch()
    {
        if (!$this->has_more_data) return;

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
        }

        $response = new Response($this->request, $raw_data, $clean_data);

        $this->responses[] = $response;

        // Move the record index pointer forward
        $this->last_fetched_index += self::PAGE_MAX_SIZE;

        return $response;
    }

    public function fetchAll()
    {
        while ($this->has_more_data)
            $this->fetch();

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
        $clean_data = array_merge(...$clean_data);

        return new Response($this->request, $raw_data, $clean_data);
    }
}

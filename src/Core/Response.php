<?php

namespace Zoho\CRM\Core;

class Response
{
    private $request;

    private $raw_data;

    private $content;

    private $paginated;

    public function __construct(Request $request, $raw_data, $content)
    {
        $this->request = $request;
        $this->raw_data = $raw_data;
        $this->content = $content;
        $this->paginated = is_array($raw_data);
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getRawData()
    {
        return $this->raw_data;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function isPaginated()
    {
        return $this->paginated;
    }
}

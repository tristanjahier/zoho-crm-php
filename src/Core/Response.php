<?php

namespace Zoho\CRM\Core;

class Response
{
    private $request;

    private $raw_data;

    private $data;

    public function __construct(Request $request, $raw_data, $data)
    {
        $this->request = $request;
        $this->raw_data = $raw_data;
        $this->data = $data;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getRawData()
    {
        return $this->raw_data;
    }

    public function getData()
    {
        return $this->data;
    }
}

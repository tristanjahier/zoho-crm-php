<?php

namespace Zoho\Crm\Contracts;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

interface ResponseParserInterface
{
    /**
     * Parse an API response and transform its content into a relevant data object.
     *
     * @param \Psr\Http\Message\ResponseInterface $httpResponse The API response to read
     * @param QueryInterface $query The origin query
     * @return ResponseInterface
     */
    public function parse(HttpResponseInterface $httpResponse, QueryInterface $query): ResponseInterface;
}

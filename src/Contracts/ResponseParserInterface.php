<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

interface ResponseParserInterface
{
    /**
     * Parse an API response and transform its content into a relevant data object.
     *
     * @param \Psr\Http\Message\ResponseInterface $httpResponse The API response to read
     * @param RequestInterface $request The origin request
     * @return ResponseInterface
     *
     * @throws \Zoho\Crm\Exceptions\UnreadableResponseException
     */
    public function parse(HttpResponseInterface $httpResponse, RequestInterface $request): ResponseInterface;
}

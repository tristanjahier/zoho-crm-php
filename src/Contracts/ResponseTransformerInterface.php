<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

interface ResponseTransformerInterface
{
    /**
     * Transform a raw response content into a relevant type or object.
     *
     * It can be used to clean up the raw response, get rid of metadata,
     * simplify the data structure, wrap into a data object etc.
     *
     * @param mixed $content The raw response content
     * @param RequestInterface $request The origin request
     * @return mixed
     */
    public function transformResponse(mixed $content, RequestInterface $request): mixed;
}

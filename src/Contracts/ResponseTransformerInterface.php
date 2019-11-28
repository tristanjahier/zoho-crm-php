<?php

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
     * @param QueryInterface $query The origin query
     * @return mixed
     */
    public function transformResponse($content, QueryInterface $query);
}

<?php

namespace Zoho\Crm\Contracts;

interface ResponseInterface
{
    /**
     * Get the raw HTTP response body.
     *
     * The return value MUST be an array of strings for paginated responses,
     * and MUST be a string for normal responses.
     *
     * @return string|string[]
     */
    public function getRawContent();

    /**
     * Get the parsed, cleaned up content.
     *
     * @return mixed
     */
    public function getContent();

    /**
     * Set the parsed, cleaned up content.
     *
     * @param mixed $content The response content
     */
    public function setContent($content);

    /**
     * Check that the response is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;
}

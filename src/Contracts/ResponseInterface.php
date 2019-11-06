<?php

namespace Zoho\Crm\Contracts;

interface ResponseInterface
{
    /**
     * Get the raw HTTP response body.
     *
     * @return string
     */
    public function getRawContent(): string;

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
     * @return void
     */
    public function setContent($content);

    /**
     * Check that the response is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;
}

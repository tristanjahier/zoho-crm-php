<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

interface ResponseInterface
{
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
     * Get the raw HTTP response objects that originated this refined response.
     *
     * The returned value is an array because some responses are an aggregate of multiple HTTP responses.
     * (For example, pages of records.)
     *
     * @return \Psr\Http\Message\ResponseInterface[]
     */
    public function getRawResponses(): array;

    /**
     * Check that the response is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;
}

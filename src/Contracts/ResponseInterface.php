<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

interface ResponseInterface
{
    /**
     * Get the parsed, cleaned up content.
     */
    public function getContent(): mixed;

    /**
     * Set the parsed, cleaned up content.
     *
     * @param mixed $content The response content
     */
    public function setContent(mixed $content);

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
     */
    public function isEmpty(): bool;
}

<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

use Stringable;
use Zoho\Crm\Support\UrlParameters;

interface HttpRequestableInterface
{
    /**
     * Get the HTTP method that must be used for the request.
     */
    public function getHttpMethod(): string;

    /**
     * Get the URL path to request.
     */
    public function getUrlPath(): string;

    /**
     * Set a query string parameter on the existing URL.
     *
     * @param string $key The parameter key
     * @param mixed $value The parameter value
     */
    public function setUrlParameter(string $key, mixed $value);

    /**
     * Get all query string parameters.
     */
    public function getUrlParameters(): UrlParameters;

    /**
     * Get the value of a query string parameter by key.
     *
     * @param string $key The parameter key
     */
    public function getUrlParameter(string $key): mixed;

    /**
     * Check if a query string parameter exists by key.
     *
     * @param string $key The parameter key
     */
    public function hasUrlParameter(string $key): bool;

    /**
     * Remove a query string parameter by key.
     *
     * @param string $key The parameter key
     */
    public function removeUrlParameter(string $key);

    /**
     * Set a header field.
     *
     * @param string $name The name of the header
     * @param string $value The value of the header
     */
    public function setHeader(string $name, string $value);

    /**
     * Remove a header field by name.
     *
     * @param string $name The name of the header
     */
    public function removeHeader(string $name);

    /**
     * Get an array of HTTP request headers.
     *
     * @return string[]
     */
    public function getHeaders(): array;

    /**
     * Set the body of the HTTP request.
     *
     * @param string|\Stringable $content The body content
     */
    public function setBody(string|Stringable $content);

    /**
     * Get the body of the HTTP request.
     */
    public function getBody(): string|Stringable;
}

<?php

namespace Zoho\Crm\Contracts;

interface RequestableInterface
{
    /**
     * Get the HTTP method that must be used for the request.
     *
     * @return string
     */
    public function getHttpMethod(): string;

    /**
     * Set the URL to request.
     *
     * @param string|null $url The new URL
     */
    public function setUrl(?string $url);

    /**
     * Get the URL to request.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Set a query string parameter on the existing URL.
     *
     * @param string $key The parameter key
     * @param mixed $value The parameter value
     */
    public function setUrlParameter(string $key, $value);

    /**
     * Set a header field.
     *
     * @param string $name The name of the header
     * @param mixed $value The value of the header
     */
    public function setHeader(string $name, $value);

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
     * @param mixed $content The body content
     */
    public function setBody($content);

    /**
     * Get the body of the HTTP request.
     *
     * @return mixed
     */
    public function getBody();
}

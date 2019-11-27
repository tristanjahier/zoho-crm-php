<?php

namespace Zoho\Crm\Contracts;

interface RequestableInterface
{
    /**
     * Get the HTTP verb/method that must be used for the request.
     *
     * @return string
     */
    public function getHttpVerb(): string;

    /**
     * Set the URI to request.
     *
     * @param string|null $uri The new URI
     */
    public function setUri(?string $uri);

    /**
     * Get the URI to request.
     *
     * @return string
     */
    public function getUri(): string;

    /**
     * Set a query string parameter on the existing URI.
     *
     * @param string $key The parameter key
     * @param mixed $value The parameter value
     */
    public function setUriParameter(string $key, $value);

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

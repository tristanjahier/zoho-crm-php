<?php

namespace Zoho\Crm\Api;

/**
 * A container for the content of an API response.
 */
class Response
{
    /** @var Query The origin query */
    private $query;

    /** @var string The raw HTTP response body */
    private $rawContent;

    /** @var mixed The parsed, cleaned up response content */
    private $content;

    /**
     * The constructor.
     *
     * @param Query $query The origin query
     * @param mixed $content The parsed response content
     * @param string $rawContent The raw response body
     */
    public function __construct(Query $query, $content, $rawContent)
    {
        $this->query = $query;
        $this->rawContent = $rawContent;
        $this->content = $content;
    }

    /**
     * Get the origin query.
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get the raw HTTP response body.
     *
     * @return string
     */
    public function getRawContent()
    {
        return $this->rawContent;
    }

    /**
     * Get the parsed, cleaned up content.
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the parsed, cleaned up content.
     *
     * @param mixed $content The response content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Check that the response is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return is_null($this->content)
            || (is_countable($this->content) && count($this->content) === 0);
    }

    /**
     * Check that the response has a content (is not empty).
     *
     * @return bool
     */
    public function hasContent()
    {
        return ! $this->isEmpty();
    }
}

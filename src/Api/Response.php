<?php

namespace Zoho\Crm\Api;

use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Contracts\QueryInterface;

/**
 * A container for the content of an API response.
 */
class Response implements ResponseInterface
{
    /** @var \Zoho\Crm\Contracts\QueryInterface The origin query */
    private $query;

    /** @var string The raw HTTP response body */
    private $rawContent;

    /** @var mixed The parsed, cleaned up response content */
    private $content;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Contracts\QueryInterface $query The origin query
     * @param mixed $content The parsed response content
     * @param string $rawContent The raw response body
     */
    public function __construct(QueryInterface $query, $content, $rawContent)
    {
        $this->query = $query;
        $this->rawContent = $rawContent;
        $this->content = $content;
    }

    /**
     * Get the origin query.
     *
     * @return \Zoho\Crm\Contracts\QueryInterface
     */
    public function getQuery(): QueryInterface
    {
        return $this->query;
    }

    /**
     * @inheritdoc
     */
    public function getRawContent(): string
    {
        return $this->rawContent;
    }

    /**
     * @inheritdoc
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isEmpty(): bool
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

<?php

namespace Zoho\Crm;

use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Contracts\RequestInterface;

/**
 * A container for the content of an API response.
 */
class Response implements ResponseInterface
{
    /** @var \Zoho\Crm\Contracts\RequestInterface The origin request */
    private $request;

    /** @var string The raw HTTP response body */
    private $rawContent;

    /** @var mixed The parsed, cleaned up response content */
    private $content;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Contracts\RequestInterface $request The origin request
     * @param mixed $content The parsed response content
     * @param string $rawContent The raw response body
     */
    public function __construct(RequestInterface $request, $content, $rawContent)
    {
        $this->request = $request;
        $this->rawContent = $rawContent;
        $this->content = $content;
    }

    /**
     * Get the origin request.
     *
     * @return \Zoho\Crm\Contracts\RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @inheritdoc
     */
    public function getRawContent()
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

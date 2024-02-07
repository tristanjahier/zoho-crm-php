<?php

declare(strict_types=1);

namespace Zoho\Crm;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Contracts\ResponseInterface;

/**
 * A container for the content of an API response.
 */
class Response implements ResponseInterface
{
    /** @var \Zoho\Crm\Contracts\RequestInterface The origin request */
    private RequestInterface $request;

    /** @var mixed The parsed, cleaned up response content */
    private mixed $content;

    /** @var \Psr\Http\Message\ResponseInterface[] The raw HTTP responses */
    private array $httpResponses = [];

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Contracts\RequestInterface $request The origin request
     * @param mixed $content The parsed response content
     * @param HttpResponseInterface|HttpResponseInterface[]|null $httpResponse (optional) The raw HTTP response(s)
     */
    public function __construct(RequestInterface $request, mixed $content, HttpResponseInterface|array $httpResponse = null)
    {
        $this->request = $request;
        $this->content = $content;

        if (isset($httpResponse)) {
            $this->httpResponses = is_array($httpResponse) ? $httpResponse : [$httpResponse];
        }
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
    public function getContent(): mixed
    {
        return $this->content;
    }

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setContent(mixed $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRawResponses(): array
    {
        return $this->httpResponses;
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
    public function hasContent(): bool
    {
        return ! $this->isEmpty();
    }
}

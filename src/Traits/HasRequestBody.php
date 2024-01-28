<?php

declare(strict_types=1);

namespace Zoho\Crm\Traits;

use Stringable;

/**
 * Basic implementation of HTTP request body for RequestInterface.
 */
trait HasRequestBody
{
    /** @var string|\Stringable The HTTP request body */
    protected string|Stringable $body = '';

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setBody(string|Stringable $content)
    {
        $this->body = $content;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBody(): string|Stringable
    {
        return $this->body;
    }
}

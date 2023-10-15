<?php

namespace Zoho\Crm\Traits;

/**
 * Basic implementation of HTTP request body for RequestInterface.
 */
trait HasRequestBody
{
    /** @var mixed The HTTP request body */
    protected $body;

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setBody($content)
    {
        $this->body = $content;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->body;
    }
}

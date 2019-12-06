<?php

namespace Zoho\Crm\Traits;

use Zoho\Crm\Support\HttpVerb;

/**
 * Basic implementation of HTTP verb/method for QueryInterface.
 */
trait HasHttpVerb
{
    /** @var string The HTTP verb/method */
    protected $httpVerb = HttpVerb::GET;

    /**
     * Set the HTTP verb/method.
     *
     * @param string $verb The HTTP verb/method to use
     * @return $this
     */
    public function setHttpVerb(string $verb)
    {
        $this->httpVerb = $verb;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHttpVerb(): string
    {
        return $this->httpVerb;
    }
}

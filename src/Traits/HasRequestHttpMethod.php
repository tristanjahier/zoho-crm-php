<?php

namespace Zoho\Crm\Traits;

use Zoho\Crm\Support\HttpMethod;

/**
 * Basic implementation of HTTP method for QueryInterface.
 */
trait HasRequestHttpMethod
{
    /** @var string The HTTP method */
    protected $httpMethod = HttpMethod::GET;

    /**
     * Set the HTTP method.
     *
     * @param string $method The HTTP method to use
     * @return $this
     */
    public function setHttpMethod(string $method)
    {
        $this->httpMethod = $method;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }
}

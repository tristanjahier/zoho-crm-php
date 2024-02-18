<?php

declare(strict_types=1);

namespace Zoho\Crm\Traits;

use Zoho\Crm\Exceptions\InvalidHttpMethodException;
use Zoho\Crm\Support\HttpMethod;

/**
 * Basic implementation of HTTP method for RequestInterface.
 */
trait HasRequestHttpMethod
{
    /** The HTTP method */
    protected string $httpMethod = HttpMethod::GET;

    /**
     * Set the HTTP method.
     *
     * @param string $method The HTTP method to use
     * @return $this
     */
    public function setHttpMethod(string $method): static
    {
        if (! HttpMethod::isValid($method)) {
            throw new InvalidHttpMethodException($method);
        }

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

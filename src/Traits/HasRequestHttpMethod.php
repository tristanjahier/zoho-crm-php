<?php

declare(strict_types=1);

namespace Zoho\Crm\Traits;

use Zoho\Crm\Support\HttpMethod;
use Zoho\Crm\Exceptions\InvalidHttpMethodException;

/**
 * Basic implementation of HTTP method for RequestInterface.
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

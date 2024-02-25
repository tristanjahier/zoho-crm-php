<?php

declare(strict_types=1);

namespace Zoho\Crm\Traits;

use Zoho\Crm\Support\UrlParameters;

/**
 * Basic implementation of URL parameters (query string) for RequestInterface.
 */
trait HasRequestUrlParameters
{
    /** The URL parameters collection */
    protected UrlParameters $urlParameters;

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setUrlParameter(string $key, mixed $value): static
    {
        $this->urlParameters->set($key, $value);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrlParameters(): UrlParameters
    {
        return $this->urlParameters;
    }

    /**
     * @inheritdoc
     */
    public function getUrlParameter(string $key): mixed
    {
        return $this->urlParameters[$key];
    }

    /**
     * @inheritdoc
     */
    public function hasUrlParameter(string $key): bool
    {
        return $this->urlParameters->has($key);
    }

    /**
     * @inheritdoc
     */
    public function removeUrlParameter(string $key): void
    {
        $this->urlParameters->unset($key);
    }

    /**
     * Remove all URL parameters.
     *
     * If an argument is passed, they will be replaced by a new set.
     *
     * @param array|\Zoho\Crm\Support\UrlParameters $parameters (optional) The new set of parameters
     * @return $this
     */
    public function resetUrlParameters(array|UrlParameters $parameters = []): static
    {
        if (! $parameters instanceof UrlParameters) {
            $parameters = new UrlParameters($parameters);
        }

        $this->urlParameters = $parameters;

        return $this;
    }

    /**
     * Shortcut method to set a URL parameter.
     *
     * @param string $key The key
     * @param mixed $value The value
     * @return $this
     */
    public function param(string $key, mixed $value): static
    {
        $this->setUrlParameter($key, $value);

        return $this;
    }

    /**
     * Shortcut method to set multiple URL parameters.
     *
     * @param array $parameters The parameters
     * @return $this
     */
    public function params(array $parameters): static
    {
        foreach ($parameters as $key => $value) {
            $this->setUrlParameter($key, $value);
        }

        return $this;
    }

    /**
     * Shortcut method to remove a URL parameter by key.
     *
     * @param string $key The parameter key
     * @return $this
     */
    public function removeParam(string $key): static
    {
        $this->removeUrlParameter($key);

        return $this;
    }
}

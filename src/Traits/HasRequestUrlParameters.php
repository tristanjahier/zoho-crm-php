<?php

namespace Zoho\Crm\Traits;

use Zoho\Crm\Support\UrlParameters;

/**
 * Basic implementation of URL parameters (query string) for QueryInterface.
 */
trait HasRequestUrlParameters
{
    /** @var \Zoho\Crm\Support\UrlParameters The URL parameters collection */
    protected $urlParameters;

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setUrlParameter(string $key, $value)
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
    public function getUrlParameter(string $key)
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
     *
     * @return void
     */
    public function removeUrlParameter(string $key)
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
    public function resetUrlParameters($parameters = [])
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
    public function param(string $key, $value)
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
    public function params(array $parameters)
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
    public function removeParam(string $key)
    {
        $this->removeUrlParameter($key);

        return $this;
    }
}

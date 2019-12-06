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
     */
    public function setUriParameter(string $key, $value)
    {
        $this->urlParameters->set($key, $value);

        return $this;
    }

    /**
     * Get the URL parameters.
     *
     * @return \Zoho\Crm\Support\UrlParameters
     */
    public function getUrlParameters()
    {
        return $this->urlParameters;
    }

    /**
     * Get the value of a URL parameter by key.
     *
     * @param string $key The parameter key
     * @return mixed
     */
    public function getUrlParameter(string $key)
    {
        return $this->urlParameters[$key];
    }

    /**
     * Check if a URL parameter exists by key.
     *
     * @param string $key The parameter key
     * @return bool
     */
    public function hasUrlParameter(string $key)
    {
        return $this->urlParameters->has($key);
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
     * Set a URL parameter.
     *
     * @param string $key The key
     * @param mixed $value The value
     * @return $this
     */
    public function param(string $key, $value)
    {
        $this->urlParameters->set($key, $value);

        return $this;
    }

    /**
     * Set multiple URL parameters.
     *
     * @param array $parameters The parameters
     * @return $this
     */
    public function params(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            $this->param($key, $value);
        }

        return $this;
    }

    /**
     * Remove a URL parameter by key.
     *
     * @param string $key The parameter key
     * @return $this
     */
    public function removeParam(string $key)
    {
        $this->urlParameters->unset($key);

        return $this;
    }
}

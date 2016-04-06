<?php

namespace Zoho\CRM\Core;

class UrlParameters implements \ArrayAccess
{
    private $parameters = [];

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function extend($custom)
    {
        if ($custom instanceof UrlParameters)
            $custom = $custom->toArray();

        return new UrlParameters(array_replace($this->parameters, $custom));
    }

    public function offsetSet($key, $value)
    {
        if ($key === null)
            $this->parameters[] = $value;
        else
            $this->parameters[$key] = $value;
    }

    public function offsetExists($key)
    {
        return isset($this->parameters[$key]);
    }

    public function offsetUnset($key)
    {
        unset($this->parameters[$key]);
    }

    public function offsetGet($key)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : null;
    }

    public function toArray()
    {
        return $this->parameters;
    }

    public function toString()
    {
        $chunks = [];

        foreach ($this->parameters as $key => $value) {
            $str = "$key";

            // Support for parameters with a value
            if ($value !== null) {
                // Support for arrays: joining elements with comas
                // i.e.: (el1,el2,el3,el4)
                if (is_array($value))
                    $value = '(' . implode(',', $value) . ')';
                $str .= '=' . urlencode($value);
            }

            $chunks[] = $str;
        }

        return implode('&', $chunks);
    }

    public function __toString()
    {
        return $this->toString();
    }
}

<?php

namespace Zoho\Crm\Api;

class IdList implements \ArrayAccess, \IteratorAggregate, \Countable
{
    private $ids = [];

    public function __construct(array $ids = [])
    {
        $this->ids = $ids;
    }

    public function offsetSet($key, $value)
    {
        if ($key === null)
            $this->ids[] = $value;
        else
            $this->ids[$key] = $value;
    }

    public function offsetExists($key)
    {
        return isset($this->ids[$key]);
    }

    public function offsetUnset($key)
    {
        unset($this->ids[$key]);
    }

    public function offsetGet($key)
    {
        return isset($this->ids[$key]) ? $this->ids[$key] : null;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->ids);
    }

    public function count()
    {
        return count($this->ids);
    }

    public function toArray()
    {
        return $this->ids;
    }

    public function toString()
    {
        return implode(';', $this->ids);
    }

    public function __toString()
    {
        return $this->toString();
    }
}

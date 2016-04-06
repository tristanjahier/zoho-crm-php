<?php

namespace Zoho\CRM\Core;

class IdList implements \ArrayAccess
{
    private $id_list = [];

    public function __construct(array $id_list = [])
    {
        $this->id_list = $id_list;
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
        return isset($this->id_list[$key]);
    }

    public function offsetUnset($key)
    {
        unset($this->id_list[$key]);
    }

    public function offsetGet($key)
    {
        return isset($this->id_list[$key]) ? $this->id_list[$key] : null;
    }

    public function toArray()
    {
        return $this->id_list;
    }

    public function toString()
    {
        return implode(';', $this->id_list);
    }

    public function __toString()
    {
        return $this->toString();
    }
}

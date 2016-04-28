<?php

namespace Zoho\CRM\Entities;

class EntityCollection implements \ArrayAccess
{
    private $entities = [];

    public function push(AbstractEntity $entity)
    {
        $this->entities[] = $entity;
    }

    public function get($index)
    {
        return isset($this->entities[$index]) ? $this->entities[$index] : null;
    }

    public function offsetSet($key, $value)
    {
        if (!$value instanceof AbstractEntity)
            trigger_error(__CLASS__ . ' values must be instances of ' . AbstractEntity::class, E_USER_ERROR);

        if ($key === null)
            $this->entities[] = $value;
        else
            $this->entities[$key] = $value;
    }

    public function offsetExists($key)
    {
        return isset($this->entities[$key]);
    }

    public function offsetUnset($key)
    {
        unset($this->entities[$key]);
    }

    public function offsetGet($key)
    {
        return $this->get($key);
    }

    public function size()
    {
        return count($this->entities);
    }

    public function getData()
    {
        return $this->entities;
    }

    public function toRawArray()
    {
        return array_map(function($entity) {
            return $entity->getData();
        }, $this->entities);
    }

    public function toArray()
    {
        $result = [];

        foreach ($this->entities as $entity)
            $result[] = $entity->toArray();

        return $result;
    }
}

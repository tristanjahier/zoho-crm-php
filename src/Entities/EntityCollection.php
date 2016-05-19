<?php

namespace Zoho\CRM\Entities;

use Zoho\CRM\Exception\InvalidComparisonOperatorException;

class EntityCollection implements \ArrayAccess, \IteratorAggregate, \Countable
{
    private $entities = [];

    public function add(AbstractEntity $entity)
    {
        $this->entities[] = $entity;
    }

    public function get($index)
    {
        return isset($this->entities[$index]) ? $this->entities[$index] : null;
    }

    public function filter(callable $filter)
    {
        $this->entities = array_filter($this->entities, $filter);
        return $this;
    }

    public function filtered(callable $filter)
    {
        $new = clone $this;
        return $new->filter($filter);
    }

    public function where($property, $operator, $value = null)
    {
        // If only two arguments are passed, we will assume
        // that the operator is implicitely an equals sign
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        return $this->filtered(function($entity) use($property, $operator, $value) {
            switch ($operator) {
                case '=':
                    return $entity->get($property) === $value;
                case '!=':
                    return $entity->get($property) !== $value;
                case '>':
                    return $entity->get($property) > $value;
                case '<':
                    return $entity->get($property) < $value;
                case '>=':
                    return $entity->get($property) >= $value;
                case '<=':
                    return $entity->get($property) <= $value;
            }

            throw new InvalidComparisonOperatorException($operator);
        });
    }

    public function valuesOf($property, $flags = [])
    {
        $property_values = [];

        // We don't use `array_column` here because it would not allow us
        // to get property values by alias
        foreach ($this->entities as $entity) {
            $property_values[] = $entity->get($property);
        }

        // If required, remove null, '' and [] values
        // Keep 'false' boolean value though, which is a significant value
        if (in_array('filter', $flags)) {
            $property_values = array_filter($property_values, function($value) {
                return $value !== null && ! empty($value);
            });
        }

        // If required, remove duplicates
        if (in_array('unique', $flags)) {
            $property_values = array_unique($property_values);
        }

        // Use `array_values` to reset array keys
        return array_values($property_values);
    }

    public function removeDuplicatesOf($property, $strict = false)
    {
        $encountered = [];

        foreach ($this->entities as $key => $entity) {
            $property_value = $entity->get($property);
            $already_exists = in_array($property_value, $encountered);

            if (($property_value === null && $strict) || $already_exists) {
                // If it already exists, or it is a null value and
                // strict mode is enabled, delete this element
                unset($this->entities[$key]);
            } elseif ($property_value !== null) {
                // If it does not exists and is NOT a null value,
                // mark it for the next round
                $encountered[] = $property_value;
            }
        }

        // Use `array_values` to reset array keys
        $this->entities = array_values($this->entities);

        return $this;
    }

    public function withoutDuplicatesOf($property, $strict = false)
    {
        $new = clone $this;
        return $new->removeDuplicatesOf($property, $strict);
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

    public function getIterator()
    {
        return new \ArrayIterator($this->entities);
    }

    public function count()
    {
        return count($this->entities);
    }

    public function size()
    {
        return $this->count();
    }

    public function rawData()
    {
        return $this->entities;
    }

    public function toRawArray()
    {
        return array_map(function($entity) {
            return $entity->rawData();
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

<?php

namespace Zoho\CRM\Entities;

use Zoho\CRM\Exception\InvalidComparisonOperatorException;
use Zoho\CRM\Api\Response;

class EntityCollection implements \ArrayAccess, \IteratorAggregate, \Countable
{
    private $entities = [];

    public function add(AbstractEntity $entity)
    {
        $this->entities[] = $entity;
    }

    public function remove($index)
    {
        unset($this->entities[$index]);
        // Rebase array indexes
        $this->entities = array_values($this->entities);
    }

    public function set($index, AbstractEntity $entity)
    {
        $this->entities[$index] = $entity;
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
        $copy = clone $this;
        return $copy->filter($filter);
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
        $copy = clone $this;
        return $copy->removeDuplicatesOf($property, $strict);
    }

    public function offsetSet($key, $value)
    {
        if ($key === null) {
            $this->add($value);
        } else {
            $this->set($key, $value);
        }
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
        return $this->size();
    }

    public function size()
    {
        return count($this->entities);
    }

    public function first()
    {
        return $this->size() > 0 ? $this->entities[0] : null;
    }

    public function last()
    {
        return $this->size() > 0 ? $this->entities[$this->size() - 1] : null;
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

        foreach ($this->entities as $entity) {
            $result[] = $entity->toArray();
        }

        return $result;
    }

    public function copy()
    {
        $copy = new EntityCollection();

        foreach ($this->entities as $entity) {
            $copy->add($entity->copy());
        }

        return $copy;
    }

    public static function createFromResponse(Response $response)
    {
        if ($response->getContent() === null) {
            return null;
        }

        $module_class = $response->getRequest()->getModuleClass();
        $entity_class = $module_class::associatedEntity();
        $collection = new EntityCollection();

        foreach ($response->getContent() as $record) {
            $collection[] = new $entity_class($record);
        }

        // Remove potential duplicates
        if ($response->containsRecords()) {
            $collection->removeDuplicatesOf($module_class::primaryKey());
        }

        return $collection;
    }
}

<?php

namespace Zoho\CRM\Entities;

use ArrayAccess;
use IteratorAggregate;
use Countable;
use Zoho\CRM\Exception\InvalidComparisonOperatorException;
use Zoho\CRM\Api\Response;

class Collection implements ArrayAccess, IteratorAggregate, Countable
{
    private $entities = [];

    public function __construct(array $entities = [])
    {
        $this->entities = $entities;
    }

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
                case 'in':
                    return in_array($entity->get($property), $value);
                case '=~':
                    return preg_match($value, $entity->get($property)) === 1;
                case 'like':
                    return $this->stringIsLike($entity->get($property), $value);
            }

            throw new InvalidComparisonOperatorException($operator);
        });
    }

    private function stringIsLike($value, $pattern)
    {
        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards.
        $pattern = str_replace('\*', '.*', $pattern);

        return preg_match('#^'.$pattern.'\z#ui', $value) === 1;
    }

    public function whereIn($property, array $values)
    {
        return $this->where($property, 'in', $values);
    }

    public function pluck($property, $index = null, $filter = false)
    {
        $values = [];

        // We can't simply use `array_column` because it would not allow us
        // to get property values by alias with the `get` method
        foreach ($this->entities as $entity) {
            // If required, index the plucked values with another property
            if (! is_null($index)) {
                $values[$entity->get($index)] = $entity->get($property);
            } else {
                $values[] = $entity->get($property);
            }
        }

        // If required, remove null, '' and [] values
        // Keep 'false' boolean value though, which is a significant value
        if ($filter) {
            $values = array_filter($values, function($value) {
                return $value !== null && ! empty($value);
            });

            if (is_null($index)) {
                // Use `array_values` to reset array keys
                $values = array_values($values);
            }
        }

        return $values;
    }

    public function valuesOf($property, $index = null, $filter = false)
    {
        return $this->pluck($property, $index, $filter);
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
        $copy = new static();

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
        $collection = new static();

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

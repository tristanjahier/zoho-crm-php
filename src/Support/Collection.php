<?php

namespace Zoho\Crm\Support;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use ArrayIterator;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\Exception\InvalidComparisonOperatorException;

class Collection implements ArrayAccess, Countable, IteratorAggregate
{
    protected $items = [];

    public function __construct($items = [])
    {
        $this->items = $this->getPlainArray($items);
    }

    public function getItems()
    {
        return $this->items;
    }

    public function push($value)
    {
        $this->items[] = $value;

        return $this;
    }

    public function set($key, $value)
    {
        $this->items[$key] = $value;

        return $this;
    }

    public function unset($key)
    {
        unset($this->items[$key]);

        return $this;
    }

    public function has($key)
    {
        return array_key_exists($key, $this->items);
    }

    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->items[$key];
        }

        return $default;
    }

    public function count()
    {
        return count($this->items);
    }

    public function isEmpty()
    {
        return empty($this->items);
    }

    public function contains($value)
    {
        return in_array($value, $this->items);
    }

    public function first($default = null)
    {
        return $this->isEmpty() ? $default : reset($this->items);
    }

    public function last($default = null)
    {
        return $this->isEmpty() ? $default : end($this->items);
    }

    public function keys()
    {
        return new static(array_keys($this->items));
    }

    public function values()
    {
        return new static(array_values($this->items));
    }

    public function column($value, $key = null)
    {
        return new static(array_column($this->items, $value, $key));
    }

    public function reverse()
    {
        return new static(array_reverse($this->items, true));
    }

    public function map(callable $callback)
    {
        $keys = array_keys($this->items);
        $items = array_map($callback, $this->items, $keys);

        return new static(array_combine($keys, $items));
    }

    public function merge($items)
    {
        return new static(array_merge($this->items, $this->getPlainArray($items)));
    }

    public function union()
    {
        $union = $this->items;

        foreach (func_get_args() as $items) {
            $union = $union + $this->getPlainArray($items);
        }

        return new static($union);
    }

    public function combine($values)
    {
        return new static(array_combine($this->items, $this->getPlainArray($values)));
    }

    public function replace($other)
    {
        return new static(array_replace($this->items, $this->getPlainArray($other)));
    }

    public function slice($offset, $length = null)
    {
        return new static(array_slice($this->items, $offset, $length, true));
    }

    public function take($limit)
    {
        if ($limit < 0) {
            return $this->slice($limit, abs($limit));
        }

        return $this->slice(0, $limit);
    }

    public function chunk($size)
    {
        $chunks = [];

        foreach (array_chunk($this->items, $size, true) as $chunk) {
            $chunks[] = new static($chunk);
        }

        return new static($chunks);
    }

    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    public function filter(callable $callback = null)
    {
        if (isset($callback)) {
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }

        return new static(array_filter($this->items));
    }

    public function reject(callable $callback)
    {
        return new static(array_filter($this->items, function ($item) use ($callback) {
            return ! $callback($item);
        }));
    }

    public function where($key, $operator, $value = null)
    {
        // If only two arguments are passed, we will assume
        // that the operator is implicitely an equals sign
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        return $this->filter(function ($item) use ($key, $operator, $value) {
            $item_value = $this->getItemPropertyValue($item, $key);

            switch ($operator) {
                case '=':
                    return $item_value === $value;
                case '!=':
                    return $item_value !== $value;
                case '>':
                    return $item_value > $value;
                case '<':
                    return $item_value < $value;
                case '>=':
                    return $item_value >= $value;
                case '<=':
                    return $item_value <= $value;
                case 'in':
                    return in_array($item_value, $this->getPlainArray($value));
                case '=~':
                    return preg_match($value, $item_value) === 1;
                case 'like':
                    return Helper::stringIsLike($item_value, $value);
                case 'not like':
                    return ! Helper::stringIsLike($item_value, $value);
            }

            throw new InvalidComparisonOperatorException($operator);
        });
    }

    public function whereIn($key, $values)
    {
        return $this->where($key, 'in', $values);
    }

    public function whereLoose($key, $value)
    {
        return $this->filter(function ($item) use ($key, $value) {
            return $this->getItemPropertyValue($item, $key) == $value;
        });
    }

    public function pluck($value, $key = null)
    {
        $results = [];

        foreach ($this->items as $i => $item) {
            $item_value = $this->getItemPropertyValue($item, $value);

            if (isset($key)) {
                // If key is strictly 'true' and not a valid array key,
                // we will simply preserve the original keys.
                $index = $key === true ? $i : $this->getItemPropertyValue($item, $key);
                $results[$index] = $item_value;
            } else {
                $results[] = $item_value;
            }
        }

        return new static($results);
    }

    public function unique(int $flags = SORT_REGULAR)
    {
        return new static(array_unique($this->items, $flags));
    }

    public function uniqueBy($key)
    {
        $unique = $this->pluck($key, true)->unique();

        return new static(array_intersect_key($this->items, $unique->getItems()));
    }

    public function duplicates()
    {
        return new static(array_diff_key($this->items, $this->unique()->getItems()));
    }

    public function duplicatesBy($key)
    {
        $unique = $this->pluck($key, true)->unique();

        return new static(array_diff_key($this->items, $unique->getItems()));
    }

    public function sort(callable $callback = null)
    {
        $items = $this->items;

        $callback ? uasort($items, $callback) : uasort($items, function ($a, $b) {
            if ($a == $b) {
                return 0;
            }

            return ($a < $b) ? -1 : 1;
        });

        return new static($items);
    }

    public function sortBy($key, $options = SORT_REGULAR, $descending = false)
    {
        $results = $this->pluck($key, true)->getItems();

        $descending ? arsort($results, $options)
                    : asort($results, $options);

        foreach (array_keys($results) as $key) {
            $results[$key] = $this->items[$key];
        }

        return new static($results);
    }

    public function sortByDesc($key, $options = SORT_REGULAR)
    {
        return $this->sortBy($key, $options, true);
    }

    public function search($value, $strict = false)
    {
        if (! is_string($value) && is_callable($value)) {
            foreach ($this->items as $key => $item) {
                if (call_user_func($value, $item, $key)) {
                    return $key;
                }
            }

            return false;
        }

        return array_search($value, $this->items, $strict);
    }

    public function binarySearch($value)
    {
        $low = 0;
        $high = $this->count() - 1;

        while ($low < $high) {
            $middle = (int) floor(($high + $low) / 2);
            $current = $this->items[$middle];

            if ($current < $value) {
                $low = $middle + 1;
            } elseif ($current > $value) {
                $high = $middle - 1;
            } else {
                return $middle;
            }
        }

        return false;
    }

    public function join($glue = '')
    {
        return implode($glue, $this->items);
    }

    public function copy()
    {
        $copy = new static();

        foreach ($this->items as $item) {
            $copy->push(clone $item);
        }

        return $copy;
    }

    protected function getPlainArray($array)
    {
        return $array instanceof self ? $array->getItems() : $array;
    }

    protected function getItemPropertyValue($item, $property)
    {
        if (is_array($item) || $item instanceof ArrayAccess) {
            return isset($item) ? $item[$property] : null;
        } elseif (is_object($item) && isset($item->{$property})) {
            return $item->{$property};
        }

        return null;
    }

    public function offsetExists($key)
    {
        return $this->has($key);
    }

    public function offsetGet($key)
    {
        return $this->get($key);
    }

    public function offsetSet($key, $value)
    {
        if ($key === null) {
            $this->push($value);
        } else {
            $this->set($key, $value);
        }
    }

    public function offsetUnset($key)
    {
        $this->unset($key);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}

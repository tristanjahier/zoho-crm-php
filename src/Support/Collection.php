<?php

declare(strict_types=1);

namespace Zoho\Crm\Support;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Zoho\Crm\Exceptions\InvalidComparisonOperatorException;

/**
 * Generic collection class for any type of object.
 *
 * It acts as a wrapper for arrays, allowing to manipulate them in an object-oriented way.
 *
 * @phpstan-consistent-constructor
 */
class Collection implements ArrayAccess, Countable, IteratorAggregate, Arrayable
{
    /** The inner array */
    protected array $items = [];

    /**
     * The constructor.
     *
     * @param array|self $items (optional) The items to put in the collection
     */
    public function __construct(array|self $items = [])
    {
        $this->items = $this->getPlainArray($items);
    }

    /**
     * Get the underlying array.
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Push an item at the end of the collection.
     *
     * @param mixed $value The item to push
     * @return $this
     */
    public function push(mixed $value): static
    {
        $this->items[] = $value;

        return $this;
    }

    /**
     * Set the item at a given index.
     *
     * @param string|int $key The index
     * @param mixed $value The item
     * @return $this
     */
    public function set(string|int $key, mixed $value): static
    {
        $this->items[$key] = $value;

        return $this;
    }

    /**
     * Remove the item at a given index.
     *
     * @param string|int $key The index
     * @return $this
     */
    public function unset(string|int $key): static
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * Determine if an item exists at a given index.
     *
     * @param string|int $key The index
     */
    public function has(string|int $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Get the item at a given index.
     *
     * @param string|int $key The index
     * @param mixed|null $default (optional) The value to return if the item is not present
     */
    public function get(string|int $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            return $this->items[$key];
        }

        return $default;
    }

    /**
     * Get the number of items in the collection.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Determine if the collection is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Determine if the collection is not empty.
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * Check if the collection contains a given item.
     *
     * @see in_array()
     *
     * @param mixed $value The item to look for
     */
    public function contains(mixed $value): bool
    {
        return in_array($value, $this->items);
    }

    /**
     * Get the first item in the collection.
     *
     * @param callable|null $callback (optional) A callback for custom comparison logic
     * @param mixed|null $default (optional) The value to return if the item is not present
     */
    public function first(callable $callback = null, mixed $default = null): mixed
    {
        if (isset($callback)) {
            foreach ($this->items as $key => $value) {
                if ($callback($value, $key) === true) {
                    return $value;
                }
            }

            return $default;
        }

        return $this->isEmpty() ? $default : $this->items[array_key_first($this->items)];
    }

    /**
     * Get the first item matching the given (key, [operator,] value) tuple.
     *
     * @see self::where()
     *
     * @param string $key The item property name
     * @param mixed $operator The comparison operator
     * @param mixed|null $value (optional) The item property value
     *
     * @throws \Zoho\Crm\Exceptions\InvalidComparisonOperatorException
     */
    public function firstWhere(string $key, mixed $operator, mixed $value = null): mixed
    {
        return $this->first($this->getWhereFilterCallback(...func_get_args()));
    }

    /**
     * Get the last item in the collection.
     *
     * @param callable|null $callback (optional) A callback for custom comparison logic
     * @param mixed|null $default (optional) The value to return if the item is not present
     */
    public function last(callable $callback = null, mixed $default = null): mixed
    {
        if (isset($callback)) {
            return $this->reverse()->first($callback, $default);
        }

        return $this->isEmpty() ? $default : $this->items[array_key_last($this->items)];
    }

    /**
     * Get the last item matching the given (key, [operator,] value) tuple.
     *
     * @see self::where()
     *
     * @param string $key The item property name
     * @param mixed $operator The comparison operator
     * @param mixed|null $value (optional) The item property value
     *
     * @throws \Zoho\Crm\Exceptions\InvalidComparisonOperatorException
     */
    public function lastWhere(string $key, mixed $operator, mixed $value = null): mixed
    {
        return $this->last($this->getWhereFilterCallback(...func_get_args()));
    }

    /**
     * Get the collection keys.
     *
     * @see array_keys()
     */
    public function keys(): static
    {
        return new static(array_keys($this->items));
    }

    /**
     * Get the collection values.
     *
     * @see array_values()
     */
    public function values(): static
    {
        return new static(array_values($this->items));
    }

    /**
     * Get the values of a given column/property from each item.
     *
     * The collection items must be arrays or objects.
     *
     * @see array_column()
     *
     * @param string|int $value The column/property to extract
     * @param string|int|null $key (optional) The column/property to use as index
     */
    public function column(string|int $value, string|int $key = null): static
    {
        return new static(array_column($this->items, $value, $key));
    }

    /**
     * Reverse items order.
     *
     * @see array_reverse()
     */
    public function reverse(): static
    {
        return new static(array_reverse($this->items, true));
    }

    /**
     * Exchange the keys with their associated values.
     *
     * @see array_flip()
     */
    public function flip(): static
    {
        return new static(array_flip($this->items));
    }

    /**
     * Apply a callback over each item and return a new collection with the results.
     *
     * @see array_map()
     *
     * @param callable $callback The callback
     */
    public function map(callable $callback): static
    {
        $keys = array_keys($this->items);
        $items = array_map($callback, $this->items, $keys);

        return new static(array_combine($keys, $items));
    }

    /**
     * Map over the collection then collapse the results.
     *
     * @param callable $callback The callback
     */
    public function flatMap(callable $callback): static
    {
        return $this->map($callback)->collapse();
    }

    /**
     * Merge the items with those of another collection.
     *
     * @see array_merge()
     *
     * @param array|self $items Another collection
     */
    public function merge(array|self $items): static
    {
        return new static(array_merge($this->items, $this->getPlainArray($items)));
    }

    /**
     * Compute the intersection with another array or collection.
     *
     * @see array_intersect()
     *
     * @param array|self $items Another collection
     */
    public function intersect(array|self $items): static
    {
        return new static(array_intersect($this->items, $this->getPlainArray($items)));
    }

    /**
     * Collapse all array/collection items into a single collection.
     *
     * Only arrays or collections will be merged, the rest will be
     * ignored. Only one depth level will be collapsed.
     */
    public function collapse(): static
    {
        $results = [];

        foreach ($this->items as $item) {
            if (! is_array($item) && ! ($item instanceof self)) {
                continue;
            }

            $results = array_merge($results, $this->getPlainArray($item));
        }

        return new static($results);
    }

    /**
     * Union the collection with one or more collections.
     *
     * @see https://www.php.net/manual/en/language.operators.array.php
     *
     * @param array|self ...$collections The collections to union
     */
    public function union(array|self ...$collections): static
    {
        $union = $this->items;

        foreach ($collections as $collection) {
            $union += $this->getPlainArray($collection);
        }

        return new static($union);
    }

    /**
     * Combine with another collection.
     *
     * @see array_combine()
     *
     * @param array|self $values The values collection
     */
    public function combine(array|self $values): static
    {
        return new static(array_combine($this->items, $this->getPlainArray($values)));
    }

    /**
     * Replace items with those of another collection by key.
     *
     * @see array_replace()
     *
     * @param array|self $other Another collection
     */
    public function replace(array|self $other): static
    {
        return new static(array_replace($this->items, $this->getPlainArray($other)));
    }

    /**
     * Push the items of another collection onto this one.
     *
     * @param array|self $items Another collection
     */
    public function concat(array|self $items): static
    {
        $results = $this->items;

        foreach ($this->getPlainArray($items) as $item) {
            $results[] = $item;
        }

        return new static($results);
    }

    /**
     * Slice the collection.
     *
     * @see array_slice()
     *
     * @param int $offset The start offset
     * @param int|null $length (optional) The length of the slice
     */
    public function slice(int $offset, int $length = null): static
    {
        return new static(array_slice($this->items, $offset, $length, true));
    }

    /**
     * Take the first or last $limit items.
     *
     * If $limit is negative, it will take the $limit last items.
     *
     * @param int $limit The number of items to take
     */
    public function take(int $limit): static
    {
        if ($limit < 0) {
            return $this->slice($limit, abs($limit));
        }

        return $this->slice(0, $limit);
    }

    /**
     * Split the collection into chunks.
     *
     * @see array_chunk()
     *
     * @param int $size The number of items per chunk
     */
    public function chunk(int $size): static
    {
        $chunks = [];

        foreach (array_chunk($this->items, $size, true) as $chunk) {
            $chunks[] = new static($chunk);
        }

        return new static($chunks);
    }

    /**
     * Reduce the collection to a single value.
     *
     * @see array_reduce()
     *
     * @param callable $callback The callback to apply to each item
     * @param mixed|null $initial (optional) The initial value to work with
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Compute the sum of the items.
     *
     * If a key or a property name is provided, its values will be summed.
     * If a callback is provided, it will be called on each item to get the value to sum.
     *
     * @param string|null $property (optional) The property to sum
     */
    public function sum(string $property = null): int|float
    {
        if (is_null($property)) {
            return array_sum($this->items);
        }

        $callback = $this->getItemPropertyRetriever($property);

        return $this->reduce(function ($sum, $item) use ($callback) {
            return $sum + $callback($item);
        }, 0);
    }

    /**
     * Filter the collection items with a callback.
     *
     * If the provided callback returns true for a given item, it is kept
     * in the collection. If it returns false, is is removed.
     *
     * @see array_filter()
     *
     * @param callable|null $callback (optional) The truth test to run on each item
     */
    public function filter(callable $callback = null): static
    {
        if (isset($callback)) {
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }

        return new static(array_filter($this->items));
    }

    /**
     * Reject items that pass a given truth test.
     *
     * Opposite of {@see self::filter()}.
     *
     * @param callable $callback The truth test to run on each item
     */
    public function reject(callable $callback): static
    {
        return new static(array_filter($this->items, function ($item) use ($callback) {
            return ! $callback($item);
        }));
    }

    /**
     * Create a callback able to filter items based on a WHERE-like comparison.
     *
     * @param string $key The item property name
     * @param mixed $operator The comparison operator
     * @param mixed|null $value (optional) The item property value
     *
     * @throws \Zoho\Crm\Exceptions\InvalidComparisonOperatorException
     */
    protected function getWhereFilterCallback(string $key, mixed $operator, mixed $value = null): callable
    {
        // If only two arguments are passed, we will assume
        // that the operator is implicitely an equals sign
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        return function ($item) use ($key, $operator, $value) {
            $itemValue = $this->getItemPropertyValue($item, $key);

            switch ($operator) {
                case '=':
                    return $itemValue === $value;
                case '!=':
                    return $itemValue !== $value;
                case '>':
                    return $itemValue > $value;
                case '<':
                    return $itemValue < $value;
                case '>=':
                    return $itemValue >= $value;
                case '<=':
                    return $itemValue <= $value;
                case 'in':
                    return in_array($itemValue, $this->getPlainArray($value));
                case 'not in':
                    return ! in_array($itemValue, $this->getPlainArray($value));
                case '=~':
                    return preg_match($value, (string) $itemValue) === 1;
                case 'like':
                    return Helper::stringIsLike((string) $itemValue, $value);
                case 'not like':
                    return ! Helper::stringIsLike((string) $itemValue, $value);
            }

            throw new InvalidComparisonOperatorException($operator);
        };
    }

    /**
     * Filter items based on a comparison tuple: (key, [operator,] value).
     *
     * The comparison operator argument is optional. You can pass 2 arguments only,
     * in this case the operator will be assumed to be "=" by default.
     *
     * @param string $key The item property name
     * @param mixed $operator The comparison operator
     * @param mixed|null $value (optional) The item property value
     *
     * @throws \Zoho\Crm\Exceptions\InvalidComparisonOperatorException
     */
    public function where(string $key, mixed $operator, mixed $value = null): static
    {
        return $this->filter($this->getWhereFilterCallback(...func_get_args()));
    }

    /**
     * Get items where the value of a given property is included in a given array.
     *
     * @see self::where()
     *
     * @param string $key The item property name
     * @param array|self $values An array or collection of values
     */
    public function whereIn(string $key, array|self $values): static
    {
        return $this->where($key, 'in', $values);
    }

    /**
     * Get items where the value of a given property is NOT included in a given array.
     *
     * @see self::where()
     *
     * @param string $key The item property name
     * @param array|self $values An array or collection of values
     */
    public function whereNotIn(string $key, array|self $values): static
    {
        return $this->where($key, 'not in', $values);
    }

    /**
     * Get items where a given property is loosely equal to a given value.
     *
     * @param string $key The item property name
     * @param mixed $value The item property value
     */
    public function whereLoose(string $key, mixed $value): static
    {
        return $this->filter(function ($item) use ($key, $value) {
            return $this->getItemPropertyValue($item, $key) == $value;
        });
    }

    /**
     * Get items where a given property is null.
     *
     * @param string $key The item property name
     */
    public function whereNull(string $key): static
    {
        return $this->where($key, null);
    }

    /**
     * Get items where a given property is NOT null.
     *
     * @param string $key The item property name
     */
    public function whereNotNull(string $key): static
    {
        return $this->where($key, '!=', null);
    }

    /**
     * Create a collection with only the items indexed by the given keys.
     *
     * You can either pass one array of keys, or multiple arguments.
     *
     * @param string|int|(string|int)[] ...$keys The keys of the items to keep
     */
    public function only(array|string|int $keys): static
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        return new static(array_intersect_key($this->items, array_flip($keys)));
    }

    /**
     * Create a collection without the items indexed by the given keys.
     *
     * You can either pass one array of keys, or multiple arguments.
     *
     * @param string|int|(string|int)[] ...$keys The keys of the items to remove
     */
    public function except(array|string|int $keys): static
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        return new static(array_diff_key($this->items, array_flip($keys)));
    }

    /**
     * Get the values of a given item property by key.
     *
     * The collection items must be arrays or objects.
     * If $key is strictly true, then the keys will be preserved.
     *
     * @param string $value The key of the values/properties to extract
     * @param string|true|null $key (optional) The key of the values/properties to use as indexes
     */
    public function pluck(string $value, string|bool $key = null): static
    {
        $results = [];

        foreach ($this->items as $i => $item) {
            $itemValue = $this->getItemPropertyValue($item, $value);

            if (isset($key)) {
                // If key is strictly 'true' and not a valid array key,
                // we will simply preserve the original keys.
                $index = $key === true ? $i : $this->getItemPropertyValue($item, $key);
                $results[$index] = $itemValue;
            } else {
                $results[] = $itemValue;
            }
        }

        return new static($results);
    }

    /**
     * Create a collection with a single occurence of each item.
     *
     * @see array_unique()
     *
     * @param int $flags (optional) The flags to pass to array_unique()
     */
    public function unique(int $flags = SORT_REGULAR): static
    {
        return new static(array_unique($this->items, $flags));
    }

    /**
     * Create a collection of unique items, based on a given item property.
     *
     * @param string $key The item property name
     */
    public function uniqueBy(string $key): static
    {
        $unique = $this->pluck($key, true)->unique();

        return new static(array_intersect_key($this->items, $unique->items()));
    }

    /**
     * Get the duplicate items.
     */
    public function duplicates(): static
    {
        return new static(array_diff_key($this->items, $this->unique()->items()));
    }

    /**
     * Get the duplicate items, based on a given item property.
     *
     * @param string $key The item property name
     */
    public function duplicatesBy(string $key): static
    {
        $unique = $this->pluck($key, true)->unique();

        return new static(array_diff_key($this->items, $unique->items()));
    }

    /**
     * Sort the items.
     *
     * @see uasort()
     *
     * @param callable|null $callback (optional) A callback for custom comparison logic
     */
    public function sort(callable $callback = null): static
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

    /**
     * Sort the items by a given property.
     *
     * @see asort()
     * @see arsort()
     *
     * @param string $key The item property name
     * @param int $options (optional) PHP SORT_* flags
     * @param bool $descending (optional) Whether to sort in descending order or not
     */
    public function sortBy(string $key, int $options = SORT_REGULAR, bool $descending = false): static
    {
        $results = $this->pluck($key, true)->items();

        $descending ? arsort($results, $options)
                    : asort($results, $options);

        foreach (array_keys($results) as $key) {
            $results[$key] = $this->items[$key];
        }

        return new static($results);
    }

    /**
     * Sort the items by a given property, in descending order.
     *
     * @param string $key The item property name
     * @param int $options (optional) PHP SORT_* flags
     */
    public function sortByDesc(string $key, int $options = SORT_REGULAR): static
    {
        return $this->sortBy($key, $options, true);
    }

    /**
     * Search a given value through the items, and return its key.
     *
     * If the value is not found, the method returns false.
     * The search always stops on the first occurence.
     *
     * @see array_search()
     *
     * @param mixed|callable $value The value to search
     * @param bool $strict (optional) Whether the comparison should be strict or not
     * @return string|int|false
     */
    public function search(mixed $value, bool $strict = false): string|int|bool
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

    /**
     * Search a given value with a binary search, and return its position.
     *
     * The collection must have ordered numeric keys, and ascendingly sorted values.
     * If the value is not found, the method returns false.
     *
     * @param mixed $value The value to search
     * @return int|false
     */
    public function binarySearch(mixed $value): int|bool
    {
        $low = 0;
        $high = $this->count() - 1;

        while ($low <= $high) {
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

    /**
     * Concatenate items as a string.
     *
     * @param string $glue (optional) The string between each item
     */
    public function join(string $glue = ''): string
    {
        return implode($glue, $this->items);
    }

    /**
     * Get one or a specified number of items randomly.
     *
     * If you don't pass any argument, you will get a single item.
     * If you pass 1, you will get a collection containing one item.
     *
     * @param int|null $number (optional) The number of items to return
     * @return mixed|static
     *
     * @throws \InvalidArgumentException
     */
    public function random(int $number = null): mixed
    {
        $requested = is_null($number) ? 1 : $number;
        $count = count($this->items);

        if ($requested > $count) {
            throw new InvalidArgumentException(
                "You are trying to get {$requested} items but the collection only contains {$count}."
            );
        }

        if (is_null($number)) {
            return $this->items[array_rand($this->items)];
        }

        if ($requested === 0) {
            return new static();
        }

        if ($requested < 0) {
            throw new InvalidArgumentException('You cannot request a negative number of items.');
        }

        return $this->only(array_rand($this->items, $requested));
    }

    /**
     * Create a deep copy of the collection.
     */
    public function copy(): static
    {
        $copy = new static();

        foreach ($this->items as $item) {
            $copy->push(clone $item);
        }

        return $copy;
    }

    /**
     * Get an instance of the base Collection class from this collection.
     */
    public function toBase(): self
    {
        return new self($this->items);
    }

    /**
     * Transform the collection into a plain array.
     *
     * If the items are {@see Arrayable}, they will be transformed into arrays too.
     */
    public function toArray(): array
    {
        return array_map(function ($value) {
            return $value instanceof Arrayable ? $value->toArray() : $value;
        }, $this->items);
    }

    /**
     * Get a plain array from a given variable.
     *
     * @param array|self $array The undetermined array/collection
     */
    protected function getPlainArray(array|self $array): array
    {
        return $array instanceof self ? $array->items() : $array;
    }

    /**
     * Get the value of a given property of an item.
     *
     * If the item is an array or implements ArrayAccess, the value will
     * be retrieved with the [] operator. If the item is an object,
     * the value will be retrieved as a public property.
     *
     * @param mixed $item The collection item
     * @param string $property The key or the property to get
     */
    protected function getItemPropertyValue(mixed $item, string $property): mixed
    {
        if (is_array($item) || $item instanceof ArrayAccess) {
            return $item[$property] ?? null;
        }

        if (is_object($item) && isset($item->{$property})) {
            return $item->{$property};
        }

        return null;
    }

    /**
     * Get a callback to retrieve the value of a given property from an item.
     *
     * If the provided argument is already a callback, it is returned as is.
     *
     * @param string|callable $property The key or the property to get, or a callback
     */
    protected function getItemPropertyRetriever(string|callable $property): callable
    {
        if (! is_string($property) && is_callable($property)) {
            return $property;
        }

        return function ($item) use ($property) {
            return $this->getItemPropertyValue($item, $property);
        };
    }

    /**
     * Determine if an item exists at a given index.
     *
     * @see \ArrayAccess
     *
     * @param mixed $key The index
     */
    public function offsetExists(mixed $key): bool
    {
        return $this->has($key);
    }

    /**
     * Get the item at a given index.
     *
     * @see \ArrayAccess
     *
     * @param mixed $key The index
     */
    public function offsetGet(mixed $key): mixed
    {
        return $this->get($key);
    }

    /**
     * Set the item at a given index.
     *
     * @see \ArrayAccess
     *
     * @param mixed $key The index
     * @param mixed $value The item
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        if ($key === null) {
            $this->push($value);
        } else {
            $this->set($key, $value);
        }
    }

    /**
     * Remove the item at a given index.
     *
     * @see \ArrayAccess
     *
     * @param mixed $key The index
     */
    public function offsetUnset(mixed $key): void
    {
        $this->unset($key);
    }

    /**
     * Get an iterator for the collection.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}

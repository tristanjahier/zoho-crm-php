<?php

declare(strict_types=1);

namespace Zoho\Crm\Support;

/**
 * Container for URL query string bits.
 */
class UrlParameters extends Collection
{
    /**
     * Override parameters values with those of another array.
     *
     * @param array|\Zoho\Crm\Support\Collection $others The other URL parameters
     */
    public function extend(array|Collection $others): static
    {
        return $this->replace($others);
    }

    /**
     * Transform a given value into its string representation.
     *
     * Boolean are stringified as "true" or "false".
     * Dates are output in the "Y-m-d H:i:s" format.
     * Arrays are represented like this: "(el1,el2,el3,...)".
     * Other types are simply casted to string.
     *
     * @param mixed $value The value to cast
     */
    protected function castValueToString(mixed $value): string
    {
        if (is_bool($value)) {
            return Helper::booleanToString($value);
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_array($value)) {
            $values = array_map(function ($val) {
                return $this->castValueToString($val);
            }, $value);

            // Join elements with comas i.e.: (el1,el2,el3,el4)
            return '(' . implode(',', $values) . ')';
        }

        return (string) $value;
    }

    /**
     * Get the string representation of a parameter.
     *
     * @param string $key The key of the parameter
     */
    public function castItemToString(string $key): string
    {
        return $this->castValueToString($this->get($key));
    }

    /**
     * Return an array of the parameters casted into strings.
     *
     * @return string[]
     */
    public function toStringArray(): array
    {
        return $this->map(function ($value) {
            return $this->castValueToString($value);
        })->items();
    }

    /**
     * Return a string representation of the URL parameters (also called query string).
     *
     * @see self::castValueToString()
     * @see http_build_query()
     * @example p1=value&p2=the%20value&p3=&p4=%2823%2C1%2C8734%29&p5=2019-11-04%2020%3A13%3A47
     */
    public function __toString(): string
    {
        return http_build_query($this->toStringArray(), '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Create an instance from a string.
     *
     * @param string $query The string to parse
     */
    public static function createFromString(string $query): static
    {
        $parameters = [];
        parse_str($query, $parameters);

        return new static($parameters);
    }

    /**
     * Create an instance from a URL.
     *
     * @param string $url The URL to parse
     */
    public static function createFromUrl(string $url): static
    {
        return static::createFromString(parse_url($url, PHP_URL_QUERY) ?? '');
    }
}

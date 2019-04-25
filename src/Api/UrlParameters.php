<?php

namespace Zoho\Crm\Api;

use DateTime;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\Support\Collection;

/**
 * Container for URL query string bits.
 */
class UrlParameters extends Collection
{
    /**
     * Override parameters values with those of another array.
     *
     * @param array|\Zoho\Crm\Support\Collection $others The other URL parameters
     * @return static
     */
    public function extend($others)
    {
        return $this->replace($others);
    }

    /**
     * Return a string representation of the URL parameters.
     *
     * Boolean are stringified as "true" or "false".
     * Dates are output in the "Y-m-d H:i:s" format.
     * Arrays are represented like this: "(el1,el2,el3,...)".
     * Parameters with null values are represented by the key only.
     *
     * @example param1=value&param2=the+value&param3&param4=(23,1,8734)
     *
     * @return string
     */
    public function __toString()
    {
        $chunks = [];

        foreach ($this->items as $key => $value) {
            $chunk = urlencode($key);

            // Support for parameters with a value
            if ($value !== null) {

                // Support for arrays
                if (is_array($value)) {
                    // Stringify boolean values
                    $value = array_map(function($el) {
                        return is_bool($el) ? Helper::booleanToString($el) : $el;
                    }, $value);

                    // Join elements with comas i.e.: (el1,el2,el3,el4)
                    $value = '(' . implode(',', $value) . ')';

                } else {
                    // Stringify boolean values
                    if (is_bool($value)) {
                        $value = Helper::booleanToString($value);
                    } elseif ($value instanceof DateTime) {
                        $value = $value->format('Y-m-d H:i:s');
                    }
                }

                $chunk .= '=' . urlencode($value);
            }

            $chunks[] = $chunk;
        }

        return implode('&', $chunks);
    }
}

<?php

namespace Zoho\Crm\Api;

use DateTime;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\Support\Collection;

class UrlParameters extends Collection
{
    public function extend($others)
    {
        return $this->replace($others);
    }

    public function __toString()
    {
        $chunks = [];

        foreach ($this->items as $key => $value) {
            $chunk = "$key";

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

                $chunk .= "=$value";
            }

            $chunks[] = $chunk;
        }

        return implode('&', $chunks);
    }
}

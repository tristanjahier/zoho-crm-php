<?php

namespace Zoho\CRM;

// Guard to avoid multiple definitions of the following functions
const FUNCTIONS_DEFINED = true;

function toSnakeCase($value)
{
    if (!ctype_lower($value)) {
        $value = preg_replace('/\s+/u', '', $value);
        $value = preg_replace('/(.)(?=[A-Z])/u', '$1'.'_', $value);
        $value = mb_strtolower($value, 'UTF-8');
    }

    return $value;
}

function toPascalCase($value)
{
    $value = ucwords(str_replace(['-', '_'], ' ', $value));
    return str_replace(' ', '', $value);
}

function toCamelCase($value)
{
    return lcfirst(toPascalCase($value));
}

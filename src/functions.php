<?php

namespace Zoho\Crm;

// Guard to avoid multiple definitions of the following functions
const FUNCTIONS_DEFINED = true;

function getModuleClassName($name)
{
    return __NAMESPACE__ . '\\Api\\Modules\\' . ucfirst($name);
}

function getMethodClassName($name)
{
    return __NAMESPACE__ . '\\Api\\Methods\\' . ucfirst($name);
}

function getEntityClassName($name)
{
    return __NAMESPACE__ . '\\Entities\\' . ucfirst($name);
}

function booleanToString($le_bool)
{
    return $le_bool ? 'true' : 'false';
}

function stringToBoolean($le_bool)
{
    switch ($le_bool) {
        case 'true':
            return true;
        case 'false':
            return false;
    }

    throw new \Exception('Invalid boolean string representation');
}

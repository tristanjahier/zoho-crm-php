<?php

namespace Zoho\CRM;

// Guard to avoid multiple definitions of the following functions
const FUNCTIONS_DEFINED = true;

function getModuleClassName($name)
{
    return __NAMESPACE__ . "\\Api\\Modules\\$name";
}

function getMethodClassName($name)
{
    return __NAMESPACE__ . "\\Api\\Methods\\$name";
}

function getEntityClassName($name)
{
    return __NAMESPACE__ . "\\Entities\\$name";
}

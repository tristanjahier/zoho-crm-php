<?php

namespace Zoho\CRM;

// Guard to avoid multiple definitions of the following functions
const FUNCTIONS_DEFINED = true;

function getModuleClassName($name)
{
    return __NAMESPACE__ . "\\Modules\\$name";
}

function getMethodClassName($name)
{
    return __NAMESPACE__ . "\\Methods\\$name";
}

function getEntityClassName($name)
{
    return __NAMESPACE__ . "\\Entities\\$name";
}

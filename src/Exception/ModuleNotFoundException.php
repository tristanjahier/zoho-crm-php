<?php

namespace Zoho\CRM\Exception;

class ModuleNotFoundException extends \Exception
{
    public function __construct($module)
    {
        parent::__construct("Module $module not found.");
    }
}

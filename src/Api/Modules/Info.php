<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Info module handler.
 */
class Info extends AbstractModule
{
    /** @inheritdoc */
    protected static $supported_methods = [
        'getModules'
    ];

    /**
     * Create a query to get information about the modules.
     *
     * @see https://www.zoho.com/crm/help/api/getmodules.html
     *
     * @return \Zoho\Crm\Api\Query
     */
    public function modules()
    {
        return $this->newQuery('getModules');
    }
}

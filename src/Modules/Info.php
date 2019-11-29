<?php

namespace Zoho\Crm\Modules;

/**
 * Info module handler.
 */
class Info extends AbstractModule
{
    /** @inheritdoc */
    protected static $supportedMethods = [
        'getModules'
    ];

    /**
     * Create a query to get information about the modules.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/getmodules.html
     *
     * @return \Zoho\Crm\Query
     */
    public function modules()
    {
        return $this->newQuery('getModules');
    }

    /**
     * Create a query to get information about the modules accessible via API.
     *
     * @return \Zoho\Crm\Query
     */
    public function apiModules()
    {
        return $this->modules()->param('type', 'api');
    }
}

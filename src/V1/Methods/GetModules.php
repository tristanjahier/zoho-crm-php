<?php

namespace Zoho\Crm\V1\Methods;

use Zoho\Crm\V1\Query;
use Zoho\Crm\Entities\Collection;
use Zoho\Crm\Entities\Module;

/**
 * @see https://www.zoho.com/crm/developer/docs/api/getmodules.html
 */
class GetModules extends AbstractMethod
{
    /**
     * @inheritdoc
     */
    public function isResponseEmpty(array $response, Query $query)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function cleanResponse(array $response, Query $query)
    {
        $entries = [];

        foreach ($response['response']['result']['row'] as $row) {
            $entries[] = $row;
        }

        return $entries;
    }

    /**
     * @inheritdoc
     */
    public function convertResponse($response, Query $query)
    {
        $modules = new Collection();

        foreach ($response as $module) {
            $modules->push(new Module($module));
        }

        return $modules;
    }
}

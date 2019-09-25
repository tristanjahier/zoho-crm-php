<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Query;

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
}

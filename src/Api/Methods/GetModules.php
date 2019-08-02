<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\ResponseDataType;
use Zoho\Crm\Api\Query;

/**
 * @see https://www.zoho.com/crm/developer/docs/api/getmodules.html
 */
class GetModules extends AbstractMethod
{
    /** @inheritdoc */
    protected static $responseType = ResponseDataType::OTHER;

    /**
     * @inheritdoc
     */
    public static function tidyResponse(array $response, Query $query)
    {
        $entries = [];

        foreach ($response['response']['result']['row'] as $row) {
            $entries[] = $row;
        }

        return $entries;
    }
}

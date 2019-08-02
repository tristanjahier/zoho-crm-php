<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\ResponseDataType;
use Zoho\Crm\Api\Query;

/**
 * @see https://www.zoho.com/crm/developer/docs/api/getusers.html
 */
class GetUsers extends AbstractMethod
{
    /** @inheritdoc */
    protected static $responseType = ResponseDataType::OTHER;

    /**
     * @inheritdoc
     */
    public static function responseContainsData(array $response, Query $query)
    {
        return ! isset($response['users']['nodata']);
    }

    /**
     * @inheritdoc
     */
    public static function tidyResponse(array $response, Query $query)
    {
        $entries = [];

        $users = $response['users']['user'];

        // Single user or multiple users?
        // If single user: wrap it in an array to process it generically
        if (isset($users['id'])) {
            $users = [$users];
        }

        foreach ($users as $user) {
            $entries[] = $user;
        }

        return $entries;
    }
}

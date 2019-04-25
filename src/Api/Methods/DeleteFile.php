<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Query;
use Zoho\Crm\Api\ResponseDataType;

/**
 * @see https://www.zoho.com/crm/help/api/deletefile.html
 */
class DeleteFile extends AbstractMethod
{
    /** @inheritdoc */
    protected static $response_type = ResponseDataType::OTHER;

    /**
     * @inheritdoc
     */
    public static function tidyResponse(array $response, Query $query)
    {
        return isset($response['response']['success']);
    }
}

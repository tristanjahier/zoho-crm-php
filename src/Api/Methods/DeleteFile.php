<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Query;
use Zoho\Crm\Api\ResponseDataType;

/**
 * @see https://www.zoho.com/crm/developer/docs/api/deletefile.html
 */
class DeleteFile extends AbstractMethod
{
    /** @inheritdoc */
    protected static $responseType = ResponseDataType::OTHER;

    /**
     * @inheritdoc
     */
    public function tidyResponse(array $response, Query $query)
    {
        return isset($response['response']['success']);
    }
}

<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Query;

/**
 * @see https://www.zoho.com/crm/developer/docs/api/deletefile.html
 */
class DeleteFile extends AbstractMethod
{
    /**
     * @inheritdoc
     */
    public function cleanResponse(array $response, Query $query)
    {
        return isset($response['response']['success']);
    }
}

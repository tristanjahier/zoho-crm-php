<?php

namespace Zoho\Crm\Methods;

use Zoho\Crm\Query;

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

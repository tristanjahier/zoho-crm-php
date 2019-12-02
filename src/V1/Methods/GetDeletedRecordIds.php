<?php

namespace Zoho\Crm\V1\Methods;

use Zoho\Crm\Contracts\ResponsePageMergerInterface;
use Zoho\Crm\V1\Query;

/**
 * @see https://www.zoho.com/crm/developer/docs/api/getdeletedrecordids.html
 */
class GetDeletedRecordIds extends AbstractMethod implements ResponsePageMergerInterface
{
    /**
     * @inheritdoc
     */
    public function isResponseEmpty(array $response, Query $query)
    {
        if (isset($response['response']['nodata'])) {
            return true;
        }

        // When going beyond the last page of results,
        // the API does not respond with a "nodata" message, as you would expect.
        // It just returns 'true' as the content of "DeletedIDs"... i.e.: {"DeletedIDs": true}
        $result = $response['response']['result']['DeletedIDs'];

        return ! isset($result) || empty($result) || $result === true;
    }

    /**
     * @inheritdoc
     */
    public function cleanResponse(array $response, Query $query)
    {
        // The result is a big string which contains IDs separated by comas
        $ids = array_filter(explode(',', $response['response']['result']['DeletedIDs']));

        return count($ids) > 0 ? $ids : [];
    }

    /**
     * @inheritdoc
     */
    public function mergePaginatedContents(...$pages)
    {
        return count($pages) > 0 ? array_merge(...$pages) : [];
    }
}

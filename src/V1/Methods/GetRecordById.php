<?php

namespace Zoho\Crm\V1\Methods;

use Zoho\Crm\V1\Query;
use Zoho\Crm\Entities\Collection;

/**
 * @see https://www.zoho.com/crm/developer/docs/api/getrecordbyid.html
 */
class GetRecordById extends GetRecords
{
    /**
     * @inheritdoc
     */
    public function cleanResponse(array $response, Query $query)
    {
        $result = parent::cleanResponse($response, $query);

        return $query->hasUrlParameter('idlist') ? $result : $result[0];
    }

    /**
     * @inheritdoc
     */
    public function convertResponse($response, Query $query)
    {
        $module = $query->getClientModule();

        if ($query->hasUrlParameter('idlist')) {
            $collection = new Collection();

            foreach ($response as $record) {
                $collection->push($module->newEntity($record));
            }

            return $collection;
        }

        return $module->newEntity($response);
    }
}

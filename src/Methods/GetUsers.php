<?php

namespace Zoho\Crm\Methods;

use Zoho\Crm\Query;
use Zoho\Crm\Entities\Collection;

/**
 * @see https://www.zoho.com/crm/developer/docs/api/getusers.html
 */
class GetUsers extends AbstractMethod
{
    /**
     * @inheritdoc
     */
    public function isResponseEmpty(array $response, Query $query)
    {
        return isset($response['users']['nodata']);
    }

    /**
     * @inheritdoc
     */
    public function getEmptyResponse(Query $query)
    {
        return new Collection();
    }

    /**
     * @inheritdoc
     */
    public function cleanResponse(array $response, Query $query)
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

    /**
     * @inheritdoc
     */
    public function convertResponse($response, Query $query)
    {
        $entities = new Collection();
        $module = $query->getClientModule();

        foreach ($response as $record) {
            $entities->push($module->newEntity($record));
        }

        return $entities;
    }
}

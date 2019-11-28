<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Contracts\ResponsePageMergerInterface;
use Zoho\Crm\Api\Query;
use Zoho\Crm\Entities\Collection;

/**
 * @see https://www.zoho.com/crm/developer/docs/api/getrecords.html
 */
class GetRecords extends AbstractMethod implements ResponsePageMergerInterface
{
    /**
     * @inheritdoc
     */
    public function isResponseEmpty(array $response, Query $query)
    {
        if (isset($response['response']['nodata'])) {
            return true;
        }

        // In "Events" module, when querying related records with "getRelatedRecords" or
        // "getSearchRecordsByPDC", sometimes when there is no data the response format is different.
        if (
            isset($response['response']['result'][$query->getModule()]) &&
            $response['response']['result'][$query->getModule()] === 'null'
        ) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function cleanResponse(array $response, Query $query)
    {
        $records = [];

        foreach ($response['response']['result'][$query->getModule()] as $rows) {
            // Single record or multiple records?
            // If single record: wrap it in an array to process it generically
            if (isset($rows['no']) && isset($rows['FL'])) {
                $rows = [$rows];
            }

            // For each record, convert it to an associative array ["field name" => "field value"]
            foreach ($rows as $row) {
                $record = [];
                $attributes = $row['FL'];

                // Single attribute or multiple attributes?
                // If single attribute: wrap it in an array to process it generically
                if (isset($attributes['content']) && isset($attributes['val'])) {
                    $attributes = [$attributes];
                }

                foreach ($attributes as $attr) {
                    $record[$attr['val']] = isset($attr['content']) ? $attr['content'] : null;
                }

                $records[] = $record;
            }
        }

        return $records;
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

    /**
     * @inheritdoc
     */
    public function mergePaginatedContents(...$pages)
    {
        $entities = new Collection();

        foreach ($pages as $page) {
            $entities = $entities->merge($page);
        }

        return $entities;
    }
}

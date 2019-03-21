<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Query;

class GetRecords extends AbstractMethod
{
    public static function responseContainsData(array $response, Query $query)
    {
        if (isset($response['response']['nodata'])) {
            return false;
        }

        // In "Events" module, when querying related records with "getRelatedRecords" or
        // "getSearchRecordsByPDC", sometimes when there is no data the response format is different.
        if (
            isset($response['response']['result'][$query->getModule()]) &&
            $response['response']['result'][$query->getModule()] === 'null'
        ) {
            return false;
        }

        return true;
    }

    public static function tidyResponse(array $response, Query $query)
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
}

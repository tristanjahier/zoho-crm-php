<?php

namespace Zoho\CRM\Api\Methods;

use Zoho\CRM\Api\Request;

class GetRecords extends AbstractMethod
{
    public static function tidyResponse(array $response, Request $request)
    {
        $records = [];

        foreach ($response['response']['result'][$request->getModule()] as $rows) {
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
                    $record[$attr['val']] = $attr['content'];
                }

                $records[] = $record;
            }
        }

        return $records;
    }
}

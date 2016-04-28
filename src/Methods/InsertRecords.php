<?php

namespace Zoho\CRM\Methods;

use Zoho\CRM\Core\Request;
use Zoho\CRM\Core\HttpVerb;

class InsertRecords extends AbstractMethod
{
    protected static $http_verb = HttpVerb::POST;

    public static function tidyResponse(array $response, Request $request)
    {
        $record_ids = [];

        // Check if records have been successfully inserted
        if (isset($response['response']['result']['recorddetail'])) {
            $records = $response['response']['result']['recorddetail'];

            // Single record or multiple records?
            // If single record: wrap it in an array to process it generically
            if (isset($records['FL'])) {
                $records = [$records];
            }

            // For each insertion, get the record ID
            foreach ($records as $record) {
                $attributes = $record['FL'];
                foreach ($attributes as $attribute) {
                    if ($attribute['val'] === 'Id') {
                        $record_ids[] = $attribute['content'];
                        break;
                    }
                }
            }
        }

        return $record_ids;
    }
}

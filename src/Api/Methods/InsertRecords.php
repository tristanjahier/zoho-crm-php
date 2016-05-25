<?php

namespace Zoho\CRM\Api\Methods;

use Zoho\CRM\Api\ResponseDataType;
use Zoho\CRM\Api\Request;
use Zoho\CRM\Api\HttpVerb;

class InsertRecords extends AbstractMethod
{
    protected static $response_type = ResponseDataType::OTHER;

    protected static $http_verb = HttpVerb::POST;

    public static function tidyResponse(array $response, Request $request)
    {
        $record_ids = [];
        $version = $request->getParameters()['version'];

        if ($version === 2) {

            // Check if records have been successfully inserted
            if ($response['response']['result']['message'] === 'Record(s) already exists') {
                // It's useless to go further because with `version` == 2,
                // Zoho does NOT support multiple records when a failure happened
                return false;
            }

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

        } elseif ($version === 4) {

            $records = $response['response']['result']['row'];

            // Single record or multiple records?
            // If single record: wrap it in an array to process it generically
            if (isset($records['no'])) {
                $records = [$records];
            }

            // For each record successfully insert, grab its ID
            foreach ($records as $record) {
                // Check for failure or duplicate notice
                if (isset($record['error']) || $record['success']['code'] === '2002') {
                    $record_ids[] = false;
                    continue;
                }

                $attributes = $record['success']['details']['FL'];

                // Single attribute or multiple attributes?
                // If single attribute: wrap it in an array to process it generically
                if (isset($attributes['content']) && isset($attributes['val'])) {
                    $attributes = [$attributes];
                }

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

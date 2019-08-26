<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\ResponseDataType;
use Zoho\Crm\Api\Query;
use Zoho\Crm\Api\HttpVerb;

/**
 * @see https://www.zoho.com/crm/developer/docs/api/insertrecords.html
 */
class InsertRecords extends AbstractMethod
{
    /** @inheritdoc */
    protected static $responseType = ResponseDataType::OTHER;

    /** @inheritdoc */
    protected static $httpVerb = HttpVerb::POST;

    /**
     * @inheritdoc
     */
    public function tidyResponse(array $response, Query $query)
    {
        $recordIds = [];
        $version = $query->getParameter('version');

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
                            $recordIds[] = $attribute['content'];
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
                    $recordIds[] = false;
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
                        $recordIds[] = $attribute['content'];
                        break;
                    }
                }
            }

        }

        return $recordIds;
    }
}

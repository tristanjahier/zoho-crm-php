<?php

namespace Zoho\CRM\Methods;

class GetRecords extends AbstractMethod
{
    public static function tidyResponse(array $response, $module)
    {
        $entries = [];

        foreach ($response['response']['result'][$module] as $rows) {
            // Determine if it is a single or multiple entries result
            $single = isset($rows['no']) && isset($rows['FL']);

            // If single-entry result: wrap it in an array in order to process it generically
            if ($single)
                $rows = [$rows];

            // For each entry, convert it to an associative array ["field name" => "field value"]
            foreach ($rows as $row) {
                $entry = [];
                foreach ($row['FL'] as $attr)
                    $entry[$attr['val']] = $attr['content'];
                $entries[] = $entry;
            }
        }

        return $entries;
    }
}

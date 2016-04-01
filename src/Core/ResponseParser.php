<?php

namespace Zoho\CRM\Core;

class ResponseParser
{
    public static function parse($response, $format)
    {
        switch ($format) {
            case ResponseFormat::XML:
                return self::parseXML($response);
                break;
            case ResponseFormat::JSON:
                return self::parseJSON($response);
                break;
            default:
                break;
        }
    }

    public static function parseXML($response)
    {
        // TODO
    }

    public static function parseJSON($response)
    {
        return json_decode($response, true);
    }

    public static function clean($module, array $raw)
    {
        $entries = [];

        foreach ($raw['response']['result'][$module] as $rows) {
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

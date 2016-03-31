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
        return json_decode($response);
    }
}

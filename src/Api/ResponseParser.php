<?php

namespace Zoho\Crm\Api;

use Zoho\Crm\Exceptions\UnreadableResponseException;
use Zoho\Crm\Exceptions\MethodNotFoundException;

class ResponseParser
{
    public static function clean(Query $query, $data)
    {
        $parsed_data = self::parse($data, $query->getFormat());

        // Detect errors in the response
        if (! self::validate($parsed_data)) {
            return null;
        }

        $api_method_handler = \Zoho\Crm\getMethodClassName($query->getMethod());
        if (! class_exists($api_method_handler)) {
            throw new MethodNotFoundException("Method handler $api_method_handler not found.");
        }

        if ($api_method_handler::responseContainsData($parsed_data)) {
            return $api_method_handler::tidyResponse($parsed_data, $query);
        } else {
            return null; // No data
        }
    }

    public static function parse($data, $format)
    {
        switch ($format) {
            case ResponseFormat::XML:
                return self::parseXml($data);
                break;
            case ResponseFormat::JSON:
                return self::parseJson($data);
                break;
            default:
                break;
        }
    }

    private static function parseXml($data)
    {
        // TODO
    }

    private static function parseJson($data)
    {
        return json_decode($data, true);
    }

    private static function validate($parsed)
    {
        if ($parsed === null || !is_array($parsed)) {
            throw new UnreadableResponseException();
            return false;
        }

        if (isset($parsed['response']['error'])) {
            ErrorHandler::handle($parsed['response']['error']);
            return false;
        }

        return true;
    }
}

<?php

namespace Zoho\CRM\Api;

use Zoho\CRM\Exception\UnreadableResponseException;
use Zoho\CRM\Exception\MethodNotFoundException;

class ResponseParser
{
    public static function clean(Request $request, $data)
    {
        $parsed_data = self::parse($data, $request->getFormat());

        // Detect errors in the response
        if (! self::validate($parsed_data)) {
            return null;
        }

        $api_method_handler = \Zoho\CRM\getMethodClassName($request->getMethod());
        if (! class_exists($api_method_handler)) {
            throw new MethodNotFoundException("Method handler $api_method_handler not found.");
        }

        if ($api_method_handler::responseContainsData($parsed_data)) {
            return $api_method_handler::tidyResponse($parsed_data, $request);
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

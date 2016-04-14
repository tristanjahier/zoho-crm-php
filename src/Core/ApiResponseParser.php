<?php

namespace Zoho\CRM\Core;

use Zoho\CRM\Exception\UnreadableResponseException;
use Zoho\CRM\Exception\MethodNotFoundException;

class ApiResponseParser
{
    public static function getData($request, $response)
    {
        $parsed_data = self::parse($response, $request->getFormat());

        if (self::validate($parsed_data)) {
            $api_method_handler = "\\Zoho\\CRM\\Methods\\" . ucfirst($request->getMethod());
            if (class_exists($api_method_handler))
                return $api_method_handler::tidyResponse($parsed_data, $request->getModule());
            else
                throw new MethodNotFoundException("Method handler $api_method_handler not found.");
        } else {
            return null;
        }
    }

    private static function parse($response, $format)
    {
        switch ($format) {
            case ResponseFormat::XML:
                return self::parseXml($response);
                break;
            case ResponseFormat::JSON:
                return self::parseJson($response);
                break;
            default:
                break;
        }
    }

    private static function parseXml($response)
    {
        // TODO
    }

    private static function parseJson($response)
    {
        return json_decode($response, true);
    }

    private static function validate($parsed)
    {
        if ($parsed === null || !is_array($parsed)) {
            throw new UnreadableResponseException();
        }

        if (isset($parsed['response']['error'])) {
            ApiErrorHandler::handle($parsed['response']['error']);
        }

        if (isset($parsed['response']['nodata'])) {
            // It is not a fatal error, so we won't raise an exception
            return false;
        }

        return true;
    }
}

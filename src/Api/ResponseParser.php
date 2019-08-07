<?php

namespace Zoho\Crm\Api;

use Zoho\Crm\Support\Helper;
use Zoho\Crm\Exceptions\UnreadableResponseException;
use Zoho\Crm\Exceptions\MethodNotFoundException;

/**
 * Static class to handle raw API responses, parse, detect errors and clean up their contents.
 */
class ResponseParser
{
    /**
     * Parse, validate then clean up a raw API response.
     *
     * @param Query $query The origin query
     * @param string $data The raw response body
     * @return mixed
     *
     * @throws \Zoho\Crm\Exceptions\MethodNotFoundException
     */
    public static function clean(Query $query, $data)
    {
        $parsedData = self::parse($data, $query->getFormat());

        self::validate($parsedData);

        $apiMethodHandler = Helper::getMethodClass($query->getMethod());
        if (! class_exists($apiMethodHandler)) {
            throw new MethodNotFoundException("Method handler $apiMethodHandler not found.");
        }

        if ($apiMethodHandler::responseContainsData($parsedData, $query)) {
            return $apiMethodHandler::tidyResponse($parsedData, $query);
        } else {
            return null; // No data
        }
    }

    /**
     * Parse a raw response body.
     *
     * @param string $data The raw response body
     * @param string $format The response format
     * @return mixed
     */
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

    /**
     * Parse a raw response body formatted in XML.
     *
     * @param string $data The raw response body
     * @return mixed
     */
    private static function parseXml($data)
    {
        // TODO
    }

    /**
     * Parse a raw response body formatted in JSON.
     *
     * @param string $data The raw response body
     * @return mixed
     */
    private static function parseJson($data)
    {
        return json_decode($data, true);
    }

    /**
     * Validate the readability and integrity of the response.
     *
     * @param array $parsed The parsed response content
     * @return void
     *
     * @throws \Zoho\Crm\Exceptions\UnreadableResponseException
     * @throws Exceptions\AbstractException
     */
    private static function validate($parsed)
    {
        if ($parsed === null || ! is_array($parsed)) {
            throw new UnreadableResponseException();
        }

        if (isset($parsed['response']['error'])) {
            ErrorHandler::handle($parsed['response']['error']);
        }
    }
}

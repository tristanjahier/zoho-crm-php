<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\ResponseDataType;
use Zoho\Crm\Api\HttpVerb;
use Zoho\Crm\Api\Query;
use Zoho\Crm\Support\ClassShortNameTrait;

/**
 * Base class of the API method handlers.
 */
abstract class AbstractMethod implements MethodInterface
{
    use ClassShortNameTrait;

    /** @var string The type of data that the method should return */
    protected static $responseType = ResponseDataType::RECORDS;

    /** @var bool Whether the method should return multiple records */
    protected static $multipleRecords = true;

    /** @var string The HTTP verb to use to make a request to the API method */
    protected static $httpVerb = HttpVerb::GET;

    /**
     * Get the name of the API method handled by this class.
     *
     * @return string
     */
    public static function name()
    {
        return lcfirst(self::getClassShortName());
    }

    /**
     * Get the type of data that the API method should return.
     *
     * @return string
     *
     * @see \Zoho\Crm\Api\ResponseDataType for a list of possible values
     */
    public static function getResponseDataType()
    {
        return static::$responseType;
    }

    /**
     * Determine if the API method should return multiple records.
     *
     * @param \Zoho\Crm\Api\Query $query (optional) The query that has been executed
     * @return bool
     */
    public static function expectsMultipleRecords(Query $query = null)
    {
        return static::$multipleRecords;
    }

    /**
     * Get the HTTP verb to use to make a request to the API method.
     *
     * @return string
     *
     * @see \Zoho\Crm\Api\HttpVerb for a list of possible values
     */
    public static function getHttpVerb()
    {
        return static::$httpVerb;
    }

    /**
     * Determine if a response contains data.
     *
     * @param array $response The parsed response
     * @param \Zoho\Crm\Api\Query $query The query that has been executed
     * @return bool
     */
    public static function responseContainsData(array $response, Query $query)
    {
        return ! isset($response['response']['nodata']);
    }
}

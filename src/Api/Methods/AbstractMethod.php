<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\HttpVerb;
use Zoho\Crm\Api\Query;
use Zoho\Crm\Support\ClassShortNameTrait;

/**
 * Default API method handler implementation.
 */
abstract class AbstractMethod implements MethodInterface
{
    use ClassShortNameTrait;

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
     * Get the HTTP verb to use to make a request to the API method.
     *
     * @return string
     *
     * @see \Zoho\Crm\Api\HttpVerb for a list of possible values
     */
    public function getHttpVerb()
    {
        return static::$httpVerb;
    }

    /**
     * @inheritdoc
     */
    public function isResponseEmpty(array $response, Query $query)
    {
        return isset($response['response']['nodata']);
    }

    /**
     * @inheritdoc
     */
    public function getEmptyResponse(Query $query)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function convertResponse($response, Query $query)
    {
        return $response;
    }
}

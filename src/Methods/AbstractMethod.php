<?php

namespace Zoho\Crm\Methods;

use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\HttpVerb;
use Zoho\Crm\Query;
use Zoho\Crm\Support\ClassShortNameTrait;

/**
 * Default API method handler implementation.
 */
abstract class AbstractMethod implements MethodInterface, ResponseTransformerInterface
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
     * @inheritdoc
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

    /**
     * @inheritdoc
     */
    public function transformResponse($content, QueryInterface $query)
    {
        if ($this->isResponseEmpty($content, $query)) {
            return $this->getEmptyResponse($query);
        }

        $clean = $this->cleanResponse($content, $query);

        return $this->convertResponse($clean, $query);
    }
}

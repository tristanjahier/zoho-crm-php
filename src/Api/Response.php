<?php

namespace Zoho\Crm\Api;

use Zoho\Crm\Support\Helper;
use Zoho\Crm\Entities\Collection;

/**
 * A container for the content of an API response.
 */
class Response
{
    /** @var string The type of data contained */
    private $type;

    /** @var Query The origin query */
    private $query;

    /** @var string The raw HTTP response body */
    private $rawContent;

    /** @var mixed The parsed, cleaned up response content */
    private $content;

    /** @var bool Whether the response should contain multiple records */
    private $hasMultipleRecords;

    /**
     * The constructor.
     *
     * @param Query $query The origin query
     * @param mixed $content The parsed response content
     * @param string $rawContent The raw response body
     */
    public function __construct(Query $query, $content, $rawContent)
    {
        $this->query = $query;
        $this->rawContent = $rawContent;
        $this->content = $content;
        $apiMethodHandler = $query->getClientMethod();
        $this->type = $apiMethodHandler->getResponseDataType();
        $this->hasMultipleRecords = $apiMethodHandler->expectsMultipleRecords($this->query);
    }

    /**
     * Get the data type of the response content.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the origin query.
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get the raw HTTP response body.
     *
     * @return string
     */
    public function getRawContent()
    {
        return $this->rawContent;
    }

    /**
     * Get the parsed, cleaned up content.
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the parsed, cleaned up content.
     *
     * @param mixed $content The response content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Check that the response is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->content === null || empty($this->content);
    }

    /**
     * Check that the response has a content (is not empty).
     *
     * @return bool
     */
    public function hasContent()
    {
        return ! $this->isEmpty();
    }

    /**
     * Check whether the response should contain records.
     *
     * @return bool
     */
    public function containsRecords()
    {
        return $this->type === ResponseDataType::RECORDS;
    }

    /**
     * Check whether the response should contain a single record.
     *
     * @return bool
     */
    public function hasSingleRecord()
    {
        return ! $this->hasMultipleRecords;
    }

    /**
     * Check whether the response should contain multiple records.
     *
     * @return bool
     */
    public function hasMultipleRecords()
    {
        return $this->hasMultipleRecords;
    }

    /**
     * Determine if the content is convertible to an entity object
     * or a collection of entities.
     *
     * @return bool
     */
    public function isConvertibleToEntity()
    {
        return $this->containsRecords()
            || $this->query->getMethod() === 'getUsers';
    }

    /**
     * Convert the response content to an entity object.
     *
     * @return \Zoho\Crm\Entities\AbstractEntity
     */
    public function toEntity()
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->query->getClientModule()->newEntity($this->content);
    }

    /**
     * Convert the response content to an entity collection.
     *
     * @return \Zoho\Crm\Entities\Collection
     */
    public function toEntityCollection()
    {
        if ($this->isEmpty()) {
            return new Collection;
        }

        $module = $this->query->getClientModule();
        $entities = [];

        foreach ($this->content as $item) {
            $entities[] = $module->newEntity($item);
        }

        return new Collection($entities);
    }
}

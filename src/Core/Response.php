<?php

namespace Zoho\CRM\Core;

use Zoho\CRM\Core\ApiResponseType;
use Zoho\CRM\Entities\EntityCollection;

class Response
{
    private $type;

    private $request;

    private $raw_data;

    private $content;

    private $paginated;

    private $has_multiple_records;

    public function __construct(Request $request, $raw_data, $content)
    {
        $this->request = $request;
        $this->raw_data = $raw_data;
        $this->content = $content;
        $this->paginated = is_array($raw_data);
        $method_class = \Zoho\CRM\getMethodClassName($this->request->getMethod());
        $this->type = $method_class::getResponseType();
        $this->has_multiple_records = $method_class::expectsMultipleRecords($this->request);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getRawData()
    {
        return $this->raw_data;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function isPaginated()
    {
        return $this->paginated;
    }

    public function containsRecords()
    {
        return $this->type === ApiResponseType::RECORDS;
    }

    public function hasSingleRecord()
    {
        return !$this->has_multiple_records;
    }

    public function hasMultipleRecords()
    {
        return $this->has_multiple_records;
    }

    public function toEntity()
    {
        $module_class = \Zoho\CRM\getModuleClassName($this->request->getModule());
        $entity_name = $module_class::associatedEntity();
        $entity_class = \Zoho\CRM\getEntityClassName($entity_name);

        // If no data has been retrieved, we cannot do anything...
        if ($this->content === null) {
            return null;
        }

        if ($this->has_multiple_records) {
            $collection = new EntityCollection();
            foreach ($this->content as $record)
                $collection[] = new $entity_class($record);
            return $collection;
        } else {
            return new $entity_class($this->content);
        }
    }
}

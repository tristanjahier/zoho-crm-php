<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Exceptions\InvalidQueryException;

/**
 * A query to get a specific record by ID.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/get-specific-record.html
 */
class GetByIdQuery extends AbstractQuery
{
    /** @var string|null The ID of the record to fetch */
    protected $recordId;

    /**
     * Set the ID of the record to fetch.
     *
     * @param string $id The ID to fetch
     * @return $this
     */
    public function setId(string $id)
    {
        $this->recordId = $id;

        return $this;
    }

    /**
     * Get the ID of the record to fetch.
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->recordId;
    }

    /**
     * @inheritdoc
     */
    public function getUri(): string
    {
        return "$this->module/$this->recordId?$this->urlParameters";
    }

    /**
     * @inheritdoc
     */
    public function validate(): void
    {
        parent::validate();

        if (is_null($this->recordId) || empty($this->recordId)) {
            throw new InvalidQueryException($this, 'the record ID must be present.');
        }
    }

    /**
     * @inheritdoc
     *
     * @return SingleRecordTransformer
     */
    public function getResponseTransformer(): ?ResponseTransformerInterface
    {
        return new SingleRecordTransformer();
    }
}

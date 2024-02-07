<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Exceptions\InvalidRequestException;

/**
 * A request to get a specific record by ID.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/get-specific-record.html
 */
class GetByIdRequest extends AbstractRequest
{
    /** @var string|null The ID of the record to fetch */
    protected ?string $recordId = null;

    /**
     * Set the ID of the record to fetch.
     *
     * @param string $id The ID to fetch
     * @return $this
     */
    public function setRecordId(string $id): static
    {
        $this->recordId = $id;

        return $this;
    }

    /**
     * Get the ID of the record to fetch.
     *
     * @return string|null
     */
    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    /**
     * @inheritdoc
     */
    public function getUrlPath(): string
    {
        return "{$this->module}/{$this->recordId}";
    }

    /**
     * @inheritdoc
     */
    public function validate(): void
    {
        parent::validate();

        if (is_null($this->recordId) || empty($this->recordId)) {
            throw new InvalidRequestException($this, 'the record ID must be present.');
        }
    }

    /**
     * @inheritdoc
     *
     * @return SingleRecordTransformer
     */
    public function getResponseTransformer(): SingleRecordTransformer
    {
        return new SingleRecordTransformer();
    }
}

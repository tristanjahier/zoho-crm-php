<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Exceptions\InvalidRequestException;
use Zoho\Crm\Support\HttpMethod;

/**
 * A request to update a specific record.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/update-specific-record.html
 */
class UpdateRequest extends AbstractRequest
{
    /** @inheritdoc */
    protected string $httpMethod = HttpMethod::PUT;

    /** The ID of the record to update */
    protected ?string $recordId = null;

    /** The new field values to save */
    protected array $recordData = [];

    /**
     * The things that the API request will trigger in Zoho CRM
     *
     * @var string[]
     */
    protected array $triggers = InsertRequest::TRIGGERS;

    /**
     * Set the ID of the record to update.
     *
     * @param string $id The record ID
     * @return $this
     */
    public function setRecordId(string $id): static
    {
        $this->recordId = $id;

        return $this;
    }

    /**
     * Get the ID of the record to update.
     */
    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    /**
     * Set the data to be updated.
     *
     * @param array|Record $data The record data
     * @return $this
     */
    public function setRecordData(array|Record $data): static
    {
        if ($data instanceof Record) {
            $data = $data->toArray();
        }

        $this->recordData = $data;

        return $this;
    }

    /**
     * Set what the API request must trigger in Zoho CRM.
     *
     * @param string[] $triggers The trigger names
     * @return $this
     */
    public function triggers(array|string $triggers): static
    {
        $triggers = is_array($triggers) ? $triggers : func_get_args();

        foreach ($triggers as $trigger) {
            if (! in_array($trigger, InsertRequest::TRIGGERS)) {
                throw new \InvalidArgumentException("'{$trigger}' is not a valid Zoho CRM API trigger.");
            }
        }

        $this->triggers = $triggers;

        return $this;
    }

    /**
     * Disable all API triggers.
     *
     * @return $this
     */
    public function disableTriggers(): static
    {
        $this->triggers = [];

        return $this;
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

        if (empty($this->recordData)) {
            throw new InvalidRequestException($this, 'the record data must be present.');
        }
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
    public function getBody(): string
    {
        return json_encode([
            'data' => [$this->recordData],
            'trigger' => $this->triggers
        ]);
    }
}

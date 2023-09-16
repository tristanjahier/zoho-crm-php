<?php

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
    protected $httpMethod = HttpMethod::PUT;

    /** @var string|null The ID of the record to update */
    protected $recordId;

    /** @var array The new field values to save */
    protected $recordData = [];

    /** @var array The things that the API request will trigger in Zoho CRM */
    protected $triggers = InsertRequest::TRIGGERS;

    /**
     * Set the ID of the record to update.
     *
     * @param string $id The record ID
     * @return $this
     */
    public function setRecordId(string $id): self
    {
        $this->recordId = $id;

        return $this;
    }

    /**
     * Get the ID of the record to update.
     *
     * @return string|null
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
    public function setRecordData($data): self
    {
        if ($data instanceof Record) {
            $data = $data->toArray();
        }

        if (! is_array($data)) {
            throw new \InvalidArgumentException('Data must be an array or an instance of '.Record::class.'.');
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
    public function triggers($triggers)
    {
        $triggers = is_array($triggers) ? $triggers : func_get_args();

        foreach ($triggers as $trigger) {
            if (! in_array($trigger, InsertRequest::TRIGGERS)) {
                throw new \InvalidArgumentException("'$trigger' is not a valid Zoho CRM API trigger.");
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
    public function disableTriggers()
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
    public function getUrl(): string
    {
        return "$this->module/$this->recordId?$this->urlParameters";
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return json_encode([
            'data' => [$this->recordData],
            'trigger' => $this->triggers
        ]);
    }
}

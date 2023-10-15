<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Exceptions\InvalidRequestException;
use Zoho\Crm\Support\HttpMethod;

/**
 * A request to insert one or many records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/insert-records.html
 */
class InsertRequest extends AbstractRequest
{
    /** @var string[] All available triggers */
    public const TRIGGERS = ['workflow', 'approval', 'blueprint'];

    /** @inheritdoc */
    protected $httpMethod = HttpMethod::POST;

    /** @var array The records to insert */
    protected $records = [];

    /** @var array The things that the API request will trigger in Zoho CRM */
    protected $triggers = self::TRIGGERS;

    /**
     * Add a record to be inserted.
     *
     * @param array|Record $data The record data or object
     * @return $this
     */
    public function addRecord($data): self
    {
        if ($data instanceof Record) {
            $data = $data->toArray();
        }

        if (! is_array($data)) {
            throw new \InvalidArgumentException('Data must be an array or an instance of '.Record::class.'.');
        }

        $this->records[] = $data;

        return $this;
    }

    /**
     * Add multiple records to be inserted.
     *
     * @param iterable $records The records
     * @return $this
     */
    public function addRecords(iterable $records): self
    {
        foreach ($records as $record) {
            $this->addRecord($record);
        }

        return $this;
    }

    /**
     * Set what the API request must trigger in Zoho CRM.
     *
     * @param string[] $triggers The trigger names
     * @return $this
     */
    public function triggers($triggers): self
    {
        $triggers = is_array($triggers) ? $triggers : func_get_args();

        foreach ($triggers as $trigger) {
            if (! in_array($trigger, self::TRIGGERS)) {
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
    public function disableTriggers(): self
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

        if (empty($this->records)) {
            throw new InvalidRequestException($this, 'records data cannot be empty.');
        }

        if (count($this->records) > 100) {
            throw new InvalidRequestException($this, 'cannot insert more than 100 records.');
        }
    }

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return "{$this->module}?{$this->urlParameters}";
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return json_encode([
            'data' => $this->records,
            'trigger' => $this->triggers
        ]);
    }
}

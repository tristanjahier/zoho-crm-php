<?php

declare(strict_types=1);

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
    protected string $httpMethod = HttpMethod::POST;

    /** @var array The records to insert */
    protected array $records = [];

    /** @var array The things that the API request will trigger in Zoho CRM */
    protected array $triggers = self::TRIGGERS;

    /**
     * Add a record to be inserted.
     *
     * @param array|Record $data The record data or object
     * @return $this
     */
    public function addRecord(array|Record $data): static
    {
        if ($data instanceof Record) {
            $data = $data->toArray();
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
    public function addRecords(iterable $records): static
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
    public function triggers(array|string $triggers): static
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
    public function getUrlPath(): string
    {
        return $this->module;
    }

    /**
     * @inheritdoc
     */
    public function getBody(): string
    {
        return json_encode([
            'data' => $this->records,
            'trigger' => $this->triggers
        ]);
    }
}

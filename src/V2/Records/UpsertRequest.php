<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

/**
 * A request to upsert (insert or update if exists) one or many records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/upsert-records.html
 */
class UpsertRequest extends InsertRequest
{
    /** @var string[] The unique fields used to check duplicates */
    protected array $duplicateCheckFields = [];

    /**
     * Set the fields that must be used for duplicate check.
     *
     * @param string[] $fields The fields (array or multiple args)
     * @return $this
     */
    public function checkDuplicatesOn(array|string $fields): static
    {
        $this->duplicateCheckFields = is_array($fields) ? $fields : func_get_args();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrlPath(): string
    {
        return "{$this->module}/upsert";
    }

    /**
     * @inheritdoc
     */
    public function getBody(): string
    {
        return json_encode([
            'data' => $this->records,
            'duplicate_check_fields' => $this->duplicateCheckFields,
            'trigger' => $this->triggers
        ]);
    }
}

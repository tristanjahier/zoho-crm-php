<?php

namespace Zoho\Crm\V2\Records;

/**
 * A query to upsert (insert or update if exists) one or many records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/upsert-records.html
 */
class UpsertQuery extends InsertQuery
{
    /** @var string[] The unique fields used to check duplicates */
    protected $duplicateCheckFields = [];

    /**
     * Set the fields that must be used for duplicate check.
     *
     * @param string[] $fields The fields (array or multiple args)
     * @return $this
     */
    public function checkDuplicatesOn($fields): self
    {
        $this->duplicateCheckFields = is_array($fields) ? $fields : func_get_args();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return "$this->module/upsert?$this->urlParameters";
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return json_encode([
            'data' => $this->records,
            'duplicate_check_fields' => $this->duplicateCheckFields,
            'trigger' => $this->triggers
        ]);
    }
}

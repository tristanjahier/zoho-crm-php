<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\ResponseTransformerInterface;

/**
 * A query to get a list of records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/get-records.html
 */
class ListQuery extends AbstractQuery
{
    /**
     * @inheritdoc
     */
    public function getUri(): string
    {
        return "$this->module?$this->urlParameters";
    }

    /**
     * @inheritdoc
     *
     * @return RecordListTransformer
     */
    public function getResponseTransformer(): ?ResponseTransformerInterface
    {
        return new RecordListTransformer();
    }

    /**
     * Select one or more fields to retrieve.
     *
     * @param string[] $fields An array of field names
     * @return $this
     */
    public function select($fields)
    {
        $fields = is_array($fields) ? $fields : func_get_args();
        $fields = $this->normalizeSelectedFields($fields);

        $currentSelection = $this->getSelectedFields();
        $newSelection = array_unique(array_merge($currentSelection, $fields));

        return $this->param('fields', implode(',', $newSelection));
    }

    /**
     * Unselect one or more fields.
     *
     * @param string[] $fields An array of field names
     * @return $this
     */
    public function unselect($fields)
    {
        $fields = is_array($fields) ? $fields : func_get_args();
        $fields = $this->normalizeSelectedFields($fields);

        $currentSelection = $this->getSelectedFields();
        $newSelection = array_diff($currentSelection, $fields);

        return $this->param('fields', implode(',', $newSelection));
    }

    /**
     * Get the selected fields.
     *
     * @return string[]
     */
    public function getSelectedFields(): array
    {
        $selection = $this->getUrlParameter('fields');

        return $this->normalizeSelectedFields(explode(',', $selection));
    }

    /**
     * Normalize field names.
     *
     * @param string[] $fields The field names
     * @return string[]
     */
    protected function normalizeSelectedFields(array $fields): array
    {
        // Cast everything to string and trim the value
        return array_filter(array_map(function ($field) {
            return trim((string) $field);
        }, $fields));
    }

    /**
     * Check if a field is selected.
     *
     * @param string $field The field to check
     * @return bool
     */
    public function hasSelected(string $field): bool
    {
        return in_array($field, $this->getSelectedFields());
    }

    /**
     * Remove selection of fields.
     *
     * @return $this
     */
    public function unselectAll()
    {
        return $this->removeParam('fields');
    }

    /**
     * Select the creation and last modification timestamps.
     *
     * @return $this
     */
    public function selectTimestamps()
    {
        return $this->select('Created_Time', 'Modified_Time');
    }

    /**
     * Select a set of default fields which are present on all records by default.
     *
     * @return $this
     */
    public function selectDefaultFields()
    {
        return $this->selectTimestamps()->select('Created_By', 'Modified_By', 'Owner');
    }
}

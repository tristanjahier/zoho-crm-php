<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use DateTimeInterface;
use Zoho\Crm\Contracts\PaginatedRequestInterface;
use Zoho\Crm\Exceptions\InvalidRequestException;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\V2\Traits\HasPagination;

/**
 * A request to get a list of records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/get-records.html
 */
class ListRequest extends AbstractRequest implements PaginatedRequestInterface
{
    use HasPagination;

    /** The maximum record modification date to fetch */
    protected ?DateTimeInterface $maxModificationDate = null;

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
    public function validate(): void
    {
        parent::validate();

        if (! $this->hasMaxModificationDate()) {
            return;
        }

        // "Modified_Time" field has to be be present in the results.
        if ($this->hasSelection() && ! $this->hasSelected('Modified_Time')) {
            $message = '"Modified_Time" field is required when using modifiedBefore().';
            throw new InvalidRequestException($this, $message);
        }

        // The request must also be sorted by "Modified_Time" in ascending order.
        if ($this->getUrlParameter('sort_by') != 'Modified_Time' || $this->getUrlParameter('sort_order') != 'asc') {
            $message = 'must be sorted by "Modified_Time" in ascending order when using modifiedBefore().';
            throw new InvalidRequestException($this, $message);
        }
    }

    /**
     * @inheritdoc
     */
    public function getResponseTransformer(): RecordListTransformer
    {
        return new RecordListTransformer();
    }

    /**
     * Select one or more fields to retrieve.
     *
     * @param string[] $fields An array of field names
     * @return $this
     */
    public function select(array|string $fields): static
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
    public function unselect(array|string $fields): static
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

        return $this->normalizeSelectedFields(explode(',', $selection ?? ''));
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
     * Check if there is a field selection.
     */
    public function hasSelection(): bool
    {
        return ! empty($this->getSelectedFields());
    }

    /**
     * Check if a field is selected.
     *
     * @param string $field The field to check
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
    public function unselectAll(): static
    {
        return $this->removeParam('fields');
    }

    /**
     * Select the creation and last modification timestamps.
     *
     * @return $this
     */
    public function selectTimestamps(): static
    {
        return $this->select('Created_Time', 'Modified_Time');
    }

    /**
     * Select a set of default fields which are present on all records by default.
     *
     * @return $this
     */
    public function selectDefaultFields(): static
    {
        return $this->selectTimestamps()->select('Created_By', 'Modified_By', 'Owner');
    }

    /**
     * Sort records by a given field, in a given direction.
     *
     * The ordering direction must be either 'asc' or 'desc'.
     *
     * @param string $field The field name
     * @param string $order (optional) The ordering direction
     * @return $this
     */
    public function sortBy(string $field, string $order = 'asc'): static
    {
        return $this->params([
            'sort_by' => $field,
            'sort_order' => $order
        ]);
    }

    /**
     * Sort records by a given field, in descending order.
     *
     * @param string $field The field name
     * @return $this
     */
    public function sortByDesc(string $field): static
    {
        return $this->sortBy($field, 'desc');
    }

    /**
     * Sort records in ascending order.
     *
     * @return $this
     */
    public function sortAsc(): static
    {
        return $this->param('sort_order', 'asc');
    }

    /**
     * Sort records in descending order.
     *
     * @return $this
     */
    public function sortDesc(): static
    {
        return $this->param('sort_order', 'desc');
    }

    /**
     * Set the minimum date for records' last modification (`Modified_Time` field).
     *
     * @param \DateTimeInterface|string|null $date A date object or a valid string
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function modifiedAfter(DateTimeInterface|string|null $date): static
    {
        if (is_null($date)) {
            return $this->removeHeader('If-Modified-Since');
        }

        $date = $this->getValidatedDateObject($date);

        return $this->setHeader('If-Modified-Since', $date->format(DATE_ATOM));
    }

    /**
     * Set the maximum date for records' last modification (`Modified_Time` field).
     *
     * @param \DateTimeInterface|string|null $date A date object or a valid string
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function modifiedBefore(DateTimeInterface|string|null $date): static
    {
        $this->maxModificationDate = is_null($date) ? null : $this->getValidatedDateObject($date);

        return $this;
    }

    /**
     * Set the minimum and maximum dates for records' last modification.
     *
     * @param \DateTimeInterface|string|null $from A date object or a valid string
     * @param \DateTimeInterface|string|null $to A date object or a valid string
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function modifiedBetween(DateTimeInterface|string|null $from, DateTimeInterface|string|null $to): static
    {
        return $this->modifiedAfter($from)->modifiedBefore($to);
    }

    /**
     * Check if the request has a maximum modification date for records.
     */
    public function hasMaxModificationDate(): bool
    {
        return isset($this->maxModificationDate);
    }

    /**
     * Get the maximum date for records' last modification.
     */
    public function getMaxModificationDate(): ?DateTimeInterface
    {
        return $this->maxModificationDate;
    }

    /**
     * Ensure to get a valid DateTime object.
     *
     * @param \DateTimeInterface|string $date A date object or a valid string
     *
     * @throws \InvalidArgumentException
     */
    protected function getValidatedDateObject(DateTimeInterface|string $date): DateTimeInterface
    {
        if (! Helper::isValidDateInput($date)) {
            throw new \InvalidArgumentException('Date must implement DateTimeInterface or be a valid date string.');
        }

        if (is_string($date)) {
            return new \DateTime($date);
        }

        return $date;
    }
}

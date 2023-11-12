<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Exceptions\InvalidRequestException;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\Support\HttpMethod;

/**
 * A request to delete many records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/delete-records.html
 */
class DeleteManyRequest extends AbstractRequest
{
    /** @inheritdoc */
    protected $httpMethod = HttpMethod::DELETE;

    /**
     * Set the IDs of the records to delete.
     *
     * @param string[] $ids The IDs to delete
     * @return $this
     */
    public function setRecordIds(array $ids): self
    {
        // Basic input filter
        $ids = array_filter(array_map('trim', $ids));

        return $this->param('ids', implode(',', $ids));
    }

    /**
     * Get the IDs of the records to delete.
     *
     * @return string[]
     */
    public function getRecordIds(): array
    {
        if (! $this->hasUrlParameter('ids')) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $this->getUrlParameter('ids'))));
    }

    /**
     * Trigger the workflow rules in Zoho upon record deletion.
     *
     * @param bool $enabled (optional) Whether the workflow rules should be triggered
     * @return $this
     */
    public function triggerWorkflowRules(bool $enabled = true): self
    {
        return $this->param('wf_trigger', Helper::booleanToString($enabled));
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
    public function validate(): void
    {
        parent::validate();

        $ids = $this->getRecordIds();

        if (empty($ids)) {
            throw new InvalidRequestException($this, 'must submit at least one record ID.');
        }

        if (count($ids) > 100) {
            throw new InvalidRequestException($this, 'cannot delete more than 100 records.');
        }
    }
}

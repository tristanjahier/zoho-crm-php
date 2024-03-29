<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Exceptions\InvalidRequestException;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\Support\HttpMethod;

/**
 * A request to delete a specific record.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/delete-specific-record.html
 */
class DeleteRequest extends AbstractRequest
{
    /** @inheritdoc */
    protected string $httpMethod = HttpMethod::DELETE;

    /** The ID of the record to delete */
    protected ?string $recordId = null;

    /**
     * Set the ID of the record to delete.
     *
     * @param string $id The ID to delete
     * @return $this
     */
    public function setRecordId(string $id): static
    {
        $this->recordId = $id;

        return $this;
    }

    /**
     * Get the ID of the record to delete.
     */
    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    /**
     * Trigger the workflow rules in Zoho upon record deletion.
     *
     * @param bool $enabled (optional) Whether the workflow rules should be triggered
     * @return $this
     */
    public function triggerWorkflowRules(bool $enabled = true): static
    {
        return $this->param('wf_trigger', Helper::booleanToString($enabled));
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
}

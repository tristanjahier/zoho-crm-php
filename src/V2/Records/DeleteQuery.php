<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Exceptions\InvalidQueryException;
use Zoho\Crm\Support\HttpMethod;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\V2\UnwrapDataTransformer;

/**
 * A query to delete a specific record.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/delete-specific-record.html
 */
class DeleteQuery extends AbstractQuery
{
    /** @inheritdoc */
    protected $httpMethod = HttpMethod::DELETE;

    /** @var string|null The ID of the record to delete */
    protected $recordId;

    /**
     * Set the ID of the record to delete.
     *
     * @param string $id The ID to delete
     * @return $this
     */
    public function setRecordId(string $id): self
    {
        $this->recordId = $id;

        return $this;
    }

    /**
     * Get the ID of the record to delete.
     *
     * @return string|null
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
    public function triggerWorkflowRules(bool $enabled = true): self
    {
        return $this->param('wf_trigger', Helper::booleanToString($enabled));
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
    public function validate(): void
    {
        parent::validate();

        if (is_null($this->recordId) || empty($this->recordId)) {
            throw new InvalidQueryException($this, 'the record ID must be present.');
        }
    }

    /**
     * @inheritdoc
     *
     * @return \Zoho\Crm\V2\UnwrapDataTransformer
     */
    public function getResponseTransformer(): ?ResponseTransformerInterface
    {
        return new UnwrapDataTransformer();
    }
}

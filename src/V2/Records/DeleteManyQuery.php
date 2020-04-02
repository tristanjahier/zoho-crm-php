<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Exceptions\InvalidQueryException;
use Zoho\Crm\Support\HttpMethod;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\V2\UnwrapDataTransformer;

/**
 * A query to delete many records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/delete-records.html
 */
class DeleteManyQuery extends AbstractQuery
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
    public function getUrl(): string
    {
        return "$this->module?$this->urlParameters";
    }

    /**
     * @inheritdoc
     */
    public function validate(): void
    {
        parent::validate();

        $ids = $this->getRecordIds();

        if (empty($ids)) {
            throw new InvalidQueryException($this, 'must submit at least one record ID.');
        }

        if (count($ids) > 100) {
            throw new InvalidQueryException($this, 'cannot delete more than 100 records.');
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

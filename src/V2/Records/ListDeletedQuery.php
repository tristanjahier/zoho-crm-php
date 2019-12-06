<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\ResponseTransformerInterface;

/**
 * A query to get a list of deleted records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/get-deleted-records.html
 */
class ListDeletedQuery extends AbstractQuery
{
    /**
     * @inheritdoc
     */
    public function getUri(): string
    {
        return "$this->module/deleted?$this->urlParameters";
    }

    /**
     * @inheritdoc
     *
     * @return DeletedRecordListTransformer
     */
    public function getResponseTransformer(): ?ResponseTransformerInterface
    {
        return new DeletedRecordListTransformer();
    }
}

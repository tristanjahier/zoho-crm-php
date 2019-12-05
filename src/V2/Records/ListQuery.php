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
     *
     * @return RecordListTransformer
     */
    public function getResponseTransformer(): ?ResponseTransformerInterface
    {
        return new RecordListTransformer();
    }
}

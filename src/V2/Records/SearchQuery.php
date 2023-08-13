<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\PaginatedQueryInterface;
use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\V2\Traits\HasPagination;

/**
 * A query to search records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/search-records.html
 */
class SearchQuery extends AbstractQuery implements PaginatedQueryInterface
{
    use HasPagination;

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return "$this->module/search?$this->urlParameters";
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
}

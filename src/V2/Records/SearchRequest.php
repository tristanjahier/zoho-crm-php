<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\PaginatedRequestInterface;
use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\V2\Traits\HasPagination;

/**
 * A request to search records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/search-records.html
 */
class SearchRequest extends AbstractRequest implements PaginatedRequestInterface
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

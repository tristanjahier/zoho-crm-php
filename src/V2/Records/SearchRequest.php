<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\PaginatedRequestInterface;
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
    public function getUrlPath(): string
    {
        return "{$this->module}/search";
    }

    /**
     * @inheritdoc
     *
     * @return RecordListTransformer
     */
    public function getResponseTransformer(): RecordListTransformer
    {
        return new RecordListTransformer();
    }
}

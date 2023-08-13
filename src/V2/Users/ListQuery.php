<?php

namespace Zoho\Crm\V2\Users;

use Zoho\Crm\Contracts\PaginatedQueryInterface;
use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\V2\AbstractQuery;
use Zoho\Crm\V2\Traits\HasPagination;

/**
 * A query to get a list of users.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/get-users.html
 */
class ListQuery extends AbstractQuery implements PaginatedQueryInterface
{
    use HasPagination;

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return "users?$this->urlParameters";
    }

    /**
     * @inheritdoc
     */
    public function validate(): void
    {
        //
    }

    /**
     * @inheritdoc
     *
     * @return RecordListTransformer
     */
    public function getResponseTransformer(): ?ResponseTransformerInterface
    {
        return new UserListTransformer();
    }
}

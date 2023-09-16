<?php

namespace Zoho\Crm\V2\Users;

use Zoho\Crm\Contracts\PaginatedRequestInterface;
use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\V2\AbstractRequest;
use Zoho\Crm\V2\Traits\HasPagination;

/**
 * A request to get a list of users.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/get-users.html
 */
class ListRequest extends AbstractRequest implements PaginatedRequestInterface
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

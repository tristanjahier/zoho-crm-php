<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Users;

use Zoho\Crm\Contracts\PaginatedRequestInterface;
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
    public function getUrlPath(): string
    {
        return 'users';
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
    public function getResponseTransformer(): UserListTransformer
    {
        return new UserListTransformer();
    }
}

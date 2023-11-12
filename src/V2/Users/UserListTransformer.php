<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Users;

use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Entities\Collection;

/**
 * A transformer for responses that consist in a list of users.
 */
class UserListTransformer implements ResponseTransformerInterface
{
    /**
     * @inheritdoc
     *
     * @return \Zoho\Crm\Entities\Collection
     */
    public function transformResponse($content, RequestInterface $request)
    {
        $users = new Collection();

        if (is_null($content)) {
            return $users;
        }

        foreach ($content['users'] as $attributes) {
            $users->push(new User($attributes));
        }

        return $users;
    }
}

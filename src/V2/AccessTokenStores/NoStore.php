<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\AccessTokenStores;

/**
 * A non-persistent, minimalist store for API access token.
 */
class NoStore extends AbstractStore
{
    /**
     * @inheritdoc
     */
    public function save(): bool
    {
        return true;
    }
}

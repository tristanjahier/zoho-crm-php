<?php

declare(strict_types=1);

namespace Zoho\Crm\V2;

use Zoho\Crm\PreferenceContainer;

/**
 * A container class for the API v2 client preferences.
 */
class Preferences extends PreferenceContainer
{
    /** @var array The available preferences and their default values */
    protected static array $defaults = [
        /**
         * Whether the new access token should be saved automatically as soon as it is refreshed.
         *
         * @var bool
         */
        'access_token_auto_save' => true,

        /**
         * The limit of validity time (in seconds) below which the access token must be refreshed automatically.
         * Set null to disable this feature.
         *
         * @var int|null
         */
        'access_token_auto_refresh_limit' => null,

        /**
         * Whether references to the raw HTTP responses should be kept inside the response object.
         *
         * @var bool
         */
        'keep_raw_responses' => true,
    ];
}

<?php

namespace Zoho\Crm\V2;

use Zoho\Crm\PreferencesContainer;

/**
 * A container class for the API v2 client preferences.
 */
class Preferences extends PreferencesContainer
{
    /** @var array The available preferences and their default values */
    protected static $defaults = [
        /**
         * Whether the new access token should be saved automatically as soon as it is refreshed.
         *
         * @var bool
         */
        'access_token_auto_save' => true,
    ];
}

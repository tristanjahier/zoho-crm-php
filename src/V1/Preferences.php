<?php

namespace Zoho\Crm\V1;

use Zoho\Crm\PreferencesContainer;

/**
 * A container class for the API v1 client preferences.
 */
class Preferences extends PreferencesContainer
{
    /** @var array The available preferences and their default values */
    protected static $defaults = [
        'exception_messages_obfuscation' => false,
        'concurrent_pagination_by_default' => false,
        'default_concurrency' => 5,
    ];
}

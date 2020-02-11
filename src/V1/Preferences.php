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
        /**
         * Hide the authentication token in exception messages.
         *
         * @var bool
         */
        'exception_messages_obfuscation' => false,

        /**
         * Use concurrency by default in all auto-paginated queries created from the client.
         *
         * @var bool
         */
        'concurrent_pagination_by_default' => false,

        /**
         * The default number of concurrent requests to use.
         * Must be a positive non-zero integer.
         *
         * @var int
         */
        'default_concurrency' => 5,
    ];
}

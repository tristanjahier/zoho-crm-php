<?php

declare(strict_types=1);

namespace Zoho\Crm;

interface NeedsClientPreferences
{
    /**
     * Set the client preferences.
     *
     * @param PreferenceContainer $preferences The client preferences container
     * @return void
     */
    public function setClientPreferences(PreferenceContainer $preferences): void;
}

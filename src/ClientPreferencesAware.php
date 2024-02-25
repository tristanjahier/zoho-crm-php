<?php

declare(strict_types=1);

namespace Zoho\Crm;

interface ClientPreferencesAware
{
    /**
     * Set the client preferences.
     *
     * @param Contracts\ClientPreferenceContainerInterface $preferences The client preferences container
     */
    public function setClientPreferences(Contracts\ClientPreferenceContainerInterface $preferences): void;
}

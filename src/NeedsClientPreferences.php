<?php

declare(strict_types=1);

namespace Zoho\Crm;

interface NeedsClientPreferences
{
    /**
     * Set the client preferences.
     *
     * @param Contracts\ClientPreferenceContainerInterface $preferences The client preferences container
     * @return void
     */
    public function setClientPreferences(Contracts\ClientPreferenceContainerInterface $preferences): void;
}

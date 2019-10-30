<?php

namespace Zoho\Crm;

use Zoho\Crm\Exceptions\UnsupportedPreferenceException;
use Zoho\Crm\Support\Collection;

/**
 * A container class for the client preferences.
 */
class Preferences extends Collection
{
    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->resetDefaults();
    }

    /**
     * Get all available preferences and their default values.
     *
     * @return array
     */
    public function defaults()
    {
        return [
            'exception_messages_obfuscation' => false,
            'concurrent_pagination_by_default' => false,
            'default_concurrency' => 5,
        ];
    }

    /**
     * Reset all preferences to their default values.
     *
     * @return void
     */
    public function resetDefaults()
    {
        $this->items = $this->defaults();
    }

    /**
     * Set the value of a given preference.
     *
     * @param string $key The name of the preference
     * @param mixed $value The value of the preference
     * @return $this
     *
     * @throws Exceptions\UnsupportedPreferenceException
     */
    public function set($key, $value)
    {
        if ($this->has($key)) {
            return parent::set($key, $value);
        } else {
            throw new UnsupportedPreferenceException($key);
        }
    }

    /**
     * Get the value of a given preference.
     *
     * @param string $key The name of the preference
     * @param mixed|null $default (optional) A default value if not found
     * @return mixed
     *
     * @throws Exceptions\UnsupportedPreferenceException
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return parent::get($key);
        } else {
            throw new UnsupportedPreferenceException($key);
        }
    }

    /**
     * Override a set of preferences with new values.
     *
     * @param array $newPrefs The set of new values
     * @return $this
     *
     * @throws Exceptions\UnsupportedPreferenceException
     */
    public function override($newPrefs)
    {
        foreach ($newPrefs as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Set the value of a given preference to true.
     *
     * @param string $key The name of the preference
     * @return $this
     *
     * @throws Exceptions\UnsupportedPreferenceException
     */
    public function enable($key)
    {
        return $this->set($key, true);
    }

    /**
     * Set the value of a given preference to false.
     *
     * @param string $key The name of the preference
     * @return $this
     *
     * @throws Exceptions\UnsupportedPreferenceException
     */
    public function disable($key)
    {
        return $this->set($key, false);
    }

    /**
     * Check if the value of a given preference is strictly true.
     *
     * @param string $key The name of the preference
     * @return bool
     *
     * @throws Exceptions\UnsupportedPreferenceException
     */
    public function isEnabled($key)
    {
        return $this->get($key) === true;
    }
}

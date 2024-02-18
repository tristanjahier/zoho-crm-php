<?php

declare(strict_types=1);

namespace Zoho\Crm;

use Zoho\Crm\Contracts\ClientPreferenceContainerInterface;
use Zoho\Crm\Exceptions\UnsupportedPreferenceException;
use Zoho\Crm\Support\Collection;

/**
 * A container class for the client preferences.
 */
abstract class PreferenceContainer implements ClientPreferenceContainerInterface
{
    /**
     * The available preferences and their default values
     *
     * @var array<string, mixed>
     */
    protected static array $defaults = [];

    /** The preferences values */
    protected Collection $items;

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
    public function defaults(): array
    {
        return static::$defaults;
    }

    /**
     * Reset all preferences to their default values.
     *
     * @return void
     */
    public function resetDefaults(): void
    {
        $this->items = new Collection(static::$defaults);
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
    public function set(string $key, mixed $value): static
    {
        if ($this->items->has($key)) {
            $this->items->set($key, $value);
            return $this;
        }

        throw new UnsupportedPreferenceException($key);
    }

    /**
     * Get the value of a given preference.
     *
     * @param string $key The name of the preference
     * @return mixed
     *
     * @throws Exceptions\UnsupportedPreferenceException
     */
    public function get(string $key): mixed
    {
        if ($this->items->has($key)) {
            return $this->items->get($key);
        }

        throw new UnsupportedPreferenceException($key);
    }

    /**
     * Override a set of preferences with new values.
     *
     * @param array $newPrefs The set of new values
     * @return $this
     *
     * @throws Exceptions\UnsupportedPreferenceException
     */
    public function override(array $newPrefs): static
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
    public function enable(string $key): static
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
    public function disable(string $key): static
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
    public function isEnabled(string $key): bool
    {
        return $this->get($key) === true;
    }

    /**
     * Check if the value of a given preference is strictly false.
     *
     * @param string $key The name of the preference
     * @return bool
     *
     * @throws Exceptions\UnsupportedPreferenceException
     */
    public function isDisabled(string $key): bool
    {
        return $this->get($key) === false;
    }

    /**
     * Check if a given preference has a non-null value.
     *
     * @param string $key The name of the preference
     * @return bool
     *
     * @throws Exceptions\UnsupportedPreferenceException
     */
    public function isSet(string $key): bool
    {
        return $this->get($key) !== null;
    }
}

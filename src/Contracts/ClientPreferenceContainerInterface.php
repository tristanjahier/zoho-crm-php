<?php

declare(strict_types=1);

namespace Zoho\Crm\Contracts;

interface ClientPreferenceContainerInterface
{
    /**
     * Get the value of the named preference.
     *
     * @param string $key The name of the preference
     * @return mixed
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedPreferenceException
     */
    public function get(string $key): mixed;

    /**
     * Set the value of the named preference.
     *
     * @param string $key The name of the preference
     * @param mixed $value The value of the preference
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedPreferenceException
     */
    public function set(string $key, mixed $value);

    /**
     * Set the value of the named preference to true.
     *
     * @param string $key The name of the preference
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedPreferenceException
     */
    public function enable(string $key);

    /**
     * Set the value of the named preference to false.
     *
     * @param string $key The name of the preference
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedPreferenceException
     */
    public function disable(string $key);

    /**
     * Check if the value of the named preference is strictly true.
     *
     * @param string $key The name of the preference
     * @return bool
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedPreferenceException
     */
    public function isEnabled(string $key): bool;

    /**
     * Check if the value of the named preference is strictly false.
     *
     * @param string $key The name of the preference
     * @return bool
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedPreferenceException
     */
    public function isDisabled(string $key): bool;

    /**
     * Check if the named preference has a non-null value.
     *
     * @param string $key The name of the preference
     * @return bool
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedPreferenceException
     */
    public function isSet(string $key): bool;
}

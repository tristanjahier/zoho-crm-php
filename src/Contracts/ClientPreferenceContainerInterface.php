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
    public function get($key);

    /**
     * Set the value of the named preference.
     *
     * @param string $key The name of the preference
     * @param mixed $value The value of the preference
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedPreferenceException
     */
    public function set($key, $value);

    /**
     * Set the value of the named preference to true.
     *
     * @param string $key The name of the preference
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedPreferenceException
     */
    public function enable($key);

    /**
     * Set the value of the named preference to false.
     *
     * @param string $key The name of the preference
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedPreferenceException
     */
    public function disable($key);

    /**
     * Check if the value of the named preference is strictly true.
     *
     * @param string $key The name of the preference
     * @return bool
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedPreferenceException
     */
    public function isEnabled($key): bool;

    /**
     * Check if the value of the named preference is strictly false.
     *
     * @param string $key The name of the preference
     * @return bool
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedPreferenceException
     */
    public function isDisabled($key): bool;

    /**
     * Check if the named preference has a non-null value.
     *
     * @param string $key The name of the preference
     * @return bool
     *
     * @throws \Zoho\Crm\Exceptions\UnsupportedPreferenceException
     */
    public function isSet($key): bool;
}

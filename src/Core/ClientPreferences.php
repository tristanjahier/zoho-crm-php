<?php

namespace Zoho\CRM\Core;

use Zoho\CRM\Exception\UnsupportedClientPreferenceException;

class ClientPreferences
{
    private $preferences = [];

    public function __construct()
    {
        $this->resetDefaults();
    }

    public function resetDefaults()
    {
        $this->preferences = [
            //
        ];
    }
    public function set($key, $value)
    {
        if (array_key_exists($key, $this->preferences))
            $this->preferences[$key] = $value;
        else
            throw new UnsupportedClientPreferenceException($key);
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->preferences))
            return $this->preferences[$key];
        else
            throw new UnsupportedClientPreferenceException($key);
    }

    public function __call($method_name, $arguments)
    {
        if (strpos($method_name, 'get') === 0) {
            $preference = \Zoho\CRM\toSnakeCase(substr($method_name, 3));
            return $this->get($preference);
        } elseif (strpos($method_name, 'set') === 0) {
            $preference = \Zoho\CRM\toSnakeCase(substr($method_name, 3));
            $this->set($preference, ...$arguments);
            return;
        } else {
            trigger_error("Call to undefined method " . __CLASS__ . "::$method_name()", E_USER_ERROR);
        }
    }

    public function toArray()
    {
        return $this->preferences;
    }
}

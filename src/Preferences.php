<?php

namespace Zoho\CRM;

use Zoho\CRM\Exception\UnsupportedPreferenceException;
use Doctrine\Common\Inflector\Inflector;

class Preferences
{
    private $preferences = [];

    public function __construct()
    {
        $this->resetDefaults();
    }

    public function resetDefaults()
    {
        $this->preferences = [
            'auto_fetch_paginated_requests' => false,
            'response_mode' => ResponseMode::WRAPPED,
            'records_as_entities' => true,
            'validate_requests' => true,
        ];
    }

    public function set($key, $value)
    {
        if (array_key_exists($key, $this->preferences))
            $this->preferences[$key] = $value;
        else
            throw new UnsupportedPreferenceException($key);
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->preferences))
            return $this->preferences[$key];
        else
            throw new UnsupportedPreferenceException($key);
    }

    public function override(array $new_prefs)
    {
        foreach ($new_prefs as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function __call($method_name, $arguments)
    {
        if (strpos($method_name, 'get') === 0) {
            $preference = Inflector::tableize(substr($method_name, 3));
            return $this->get($preference);
        } elseif (strpos($method_name, 'set') === 0) {
            $preference = Inflector::tableize(substr($method_name, 3));
            // PHP 5.5 compatible code:
            call_user_func_array([$this, 'set'], array_merge([$preference], $arguments));
            // PHP 5.6+, using splat operator:
            // $this->set($preference, ...$arguments);
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

<?php

namespace Zoho\CRM;

use Zoho\CRM\Exception\UnsupportedClientPreferenceException;
use Doctrine\Common\Inflector\Inflector;

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
            'auto_fetch_paginated_requests' => true,
            'response_mode' => ClientResponseMode::WRAPPED,
            'records_as_entities' => true
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
            $preference = Inflector::tableize(substr($method_name, 3));
            return $this->get($preference);
        } elseif (strpos($method_name, 'set') === 0) {
            $preference = Inflector::tableize(substr($method_name, 3));
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

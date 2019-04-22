<?php

namespace Zoho\Crm;

use Zoho\Crm\Exceptions\UnsupportedPreferenceException;
use Zoho\Crm\Support\Collection;

class Preferences extends Collection
{
    public function __construct()
    {
        $this->resetDefaults();
    }

    public function defaults()
    {
        return [
            'exception_messages_obfuscation' => false,
        ];
    }

    public function resetDefaults()
    {
        $this->items = $this->defaults();
    }

    public function set($key, $value)
    {
        if ($this->has($key)) {
            return parent::set($key, $value);
        } else {
            throw new UnsupportedPreferenceException($key);
        }
    }

    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return parent::get($key);
        } else {
            throw new UnsupportedPreferenceException($key);
        }
    }

    public function override($new_prefs)
    {
        foreach ($new_prefs as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    public function enable($key)
    {
        return $this->set($key, true);
    }

    public function disable($key)
    {
        return $this->set($key, false);
    }

    public function isEnabled($key)
    {
        return $this->get($key) === true;
    }
}

<?php

namespace Zoho\Crm;

use Zoho\Crm\Support\Collection;

/**
 * Container for Zoho record IDs.
 */
class IdList extends Collection
{
    /**
     * Return a string representation of the list.
     *
     * It concatenates the IDs, separated by semicolons.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->join(';');
    }
}

<?php

namespace Zoho\Crm\Api;

use Zoho\Crm\Support\Collection;

class IdList extends Collection
{
    public function __toString()
    {
        return $this->join(';');
    }
}

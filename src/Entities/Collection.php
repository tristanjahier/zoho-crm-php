<?php

namespace Zoho\Crm\Entities;

use Zoho\Crm\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
    public function toRawArray()
    {
        return $this->map(function ($entity) {
            return $entity->rawData();
        })->items();
    }

    protected function getItemPropertyValue($item, $property)
    {
        return $item->get($property);
    }
}

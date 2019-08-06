<?php

namespace Zoho\Crm\Entities;

use Zoho\Crm\Support\Collection as BaseCollection;

/**
 * Collection of entities.
 */
class Collection extends BaseCollection
{
    /**
     * Find an entity by primary key.
     *
     * @param string $key The entity key
     * @return AbstractEntity|null
     */
    public function find($key)
    {
        return $this->first(function ($entity) use ($key) {
            return $entity->key() === $key;
        });
    }

    /**
     * Get the primary keys of the entities.
     *
     * @return \Zoho\Crm\Support\Collection
     */
    public function entityKeys()
    {
        return $this->map(function ($entity) {
            return $entity->key();
        })->toBase();
    }

    /**
     * Get the value of an attribute from a given entity.
     *
     * @param AbstractEntity $item The entity
     * @param string $attribute The name of the attribute
     * @return string|null
     */
    protected function getItemPropertyValue($item, $attribute)
    {
        return $item->get($attribute);
    }
}

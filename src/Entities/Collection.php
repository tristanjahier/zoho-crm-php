<?php

namespace Zoho\Crm\Entities;

use Zoho\Crm\Support\Collection as BaseCollection;

/**
 * Collection of entities.
 */
class Collection extends BaseCollection
{
    /**
     * Find an entity by ID.
     *
     * @param string $id The entity ID
     * @return Entity|null
     */
    public function find($id)
    {
        return $this->first(function ($entity) use ($id) {
            return $entity->getId() === $id;
        });
    }

    /**
     * Get the IDs of the entities.
     *
     * @return \Zoho\Crm\Support\Collection
     */
    public function entityIds()
    {
        return $this->map(function ($entity) {
            return $entity->getId();
        })->toBase();
    }

    /**
     * Return a collection of unique entities, based on their IDs.
     *
     * @return static
     */
    public function uniqueById()
    {
        if ($this->isEmpty()) {
            return $this;
        }

        return $this->uniqueBy($this->first()::idName());
    }

    /**
     * Get the value of an attribute from a given entity.
     *
     * @param Entity $item The entity
     * @param string $attribute The name of the attribute
     * @return string|null
     */
    protected function getItemPropertyValue($item, $attribute)
    {
        return $item->get($attribute);
    }
}

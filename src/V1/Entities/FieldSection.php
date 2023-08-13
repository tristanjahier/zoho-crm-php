<?php

namespace Zoho\Crm\V1\Entities;

/**
 * Module field section entity.
 */
class FieldSection extends ImmutableEntity
{
    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [], Client $client = null)
    {
        parent::__construct($attributes, $client);

        $fields = new Collection();

        if (isset($attributes['FL'])) {
            foreach ($attributes['FL'] as $fieldAttributes) {
                $fields->push(new Field($fieldAttributes));
            }
        }

        $this->attributes['FL'] = $fields;
    }
}

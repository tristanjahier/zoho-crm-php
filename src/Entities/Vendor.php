<?php

namespace Zoho\Crm\Entities;

/**
 * Vendor entity class.
 */
class Vendor extends AbstractEntity
{
    /** @inheritdoc */
    protected static $property_aliases = [
        'id'               => 'VENDORID',
        'owner'            => 'SMOWNERID',
        'owner_name'       => 'Vendor Owner',
        'name'             => 'Vendor Name',
        'tag'              => 'Tag',
        'description'      => 'Description',
        'created_at'       => 'Created Time',
        'modified_at'      => 'Modified Time',
    ];
}

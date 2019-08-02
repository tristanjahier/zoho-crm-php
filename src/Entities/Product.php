<?php

namespace Zoho\Crm\Entities;

/**
 * Product entity class.
 */
class Product extends AbstractEntity
{
    /** @inheritdoc */
    protected static $propertyAliases = [
        'id'               => 'PRODUCTID',
        'owner'            => 'SMOWNERID',
        'owner_name'       => 'Product Owner',
        'name'             => 'Product Name',
        'active'           => 'Product Active',
        'vendor'           => 'VENDORID',
        'vendor_name'      => 'Vendor Name',
        'unit_price'       => 'Unit Price',
        'commission_rate'  => 'Commission Rate',
        'description'      => 'Description',
        'created_by'       => 'SMCREATORID',
        'created_by_name'  => 'Created By',
        'modified_by'      => 'MODIFIEDBY',
        'modified_by_name' => 'Modified By',
        'created_at'       => 'Created Time',
        'modified_at'      => 'Modified Time',
    ];
}

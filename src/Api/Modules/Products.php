<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Products module handler.
 *
 * @see https://www.zoho.com/crm/help/api/modules-fields.html#Products
 */
class Products extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $primary_key = 'PRODUCTID';

    /** @inheritdoc */
    protected static $associated_entity = \Zoho\Crm\Entities\Product::class;

    /** @inheritdoc */
    protected static $supported_methods = [
        'getFields',
        'getRecordById',
        'getRecords',
        'getMyRecords',
        'searchRecords',
        'insertRecords',
        'updateRecords',
        'deleteRecords',
        'getDeletedRecordIds',
        'getRelatedRecords',
        'getSearchRecordsByPDC',
        'deleteFile',
    ];
}

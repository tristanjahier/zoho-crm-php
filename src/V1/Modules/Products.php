<?php

namespace Zoho\Crm\V1\Modules;

/**
 * Products module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/modules-fields.html#Products
 */
class Products extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\V1\Entities\Records\Product::class;

    /** @inheritdoc */
    protected static $supportedMethods = [
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

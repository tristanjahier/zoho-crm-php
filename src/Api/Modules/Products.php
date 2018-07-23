<?php

namespace Zoho\Crm\Api\Modules;

class Products extends AbstractRecordsModule
{
    protected static $primary_key = 'PRODUCTID';

    protected static $associated_entity = \Zoho\Crm\Entities\Product::class;

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

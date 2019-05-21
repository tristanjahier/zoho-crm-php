<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Vendors module handler.
 *
 * @see https://www.zoho.com/crm/help/api/modules-fields.html#Vendors
 */
class Vendors extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $primary_key = 'VENDORID';

    /** @inheritdoc */
    protected static $associated_entity = \Zoho\Crm\Entities\Vendor::class;

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

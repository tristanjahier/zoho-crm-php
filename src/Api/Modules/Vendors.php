<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Vendors module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/modules-fields.html#Vendors
 */
class Vendors extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $primaryKey = 'VENDORID';

    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\Entities\Vendor::class;

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

<?php

namespace Zoho\Crm\V1\Modules;

/**
 * Invoices module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/modules-fields.html#Invoices
 */
class Invoices extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\Entities\Records\Invoice::class;

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

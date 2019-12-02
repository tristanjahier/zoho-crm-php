<?php

namespace Zoho\Crm\V1\Modules;

/**
 * Calls module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/modules-fields.html#Calls
 */
class Calls extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\Entities\Records\Call::class;

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
    ];
}

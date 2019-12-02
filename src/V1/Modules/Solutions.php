<?php

namespace Zoho\Crm\V1\Modules;

/**
 * Solutions module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/modules-fields.html#Solutions
 */
class Solutions extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\Entities\Records\Solution::class;

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

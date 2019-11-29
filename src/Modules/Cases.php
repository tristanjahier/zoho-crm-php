<?php

namespace Zoho\Crm\Modules;

/**
 * Cases module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/modules-fields.html#Cases
 */
class Cases extends AbstractRecordsModule
{
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

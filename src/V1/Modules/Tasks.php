<?php

namespace Zoho\Crm\V1\Modules;

/**
 * Tasks module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/modules-fields.html#Tasks
 */
class Tasks extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\Entities\Records\Task::class;

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

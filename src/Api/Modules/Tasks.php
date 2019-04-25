<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Tasks module handler.
 *
 * @see https://www.zoho.com/crm/help/api/modules-fields.html#Tasks
 */
class Tasks extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $primary_key = 'ACTIVITYID';

    /** @inheritdoc */
    protected static $associated_entity = \Zoho\Crm\Entities\Task::class;

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

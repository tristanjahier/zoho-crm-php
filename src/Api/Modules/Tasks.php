<?php

namespace Zoho\CRM\Api\Modules;

class Tasks extends AbstractRecordsModule
{
    protected static $primary_key = 'ACTIVITYID';

    protected static $associated_entity = \Zoho\CRM\Entities\Task::class;

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
    ];
}

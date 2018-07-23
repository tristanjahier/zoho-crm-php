<?php

namespace Zoho\Crm\Api\Modules;

class Notes extends AbstractRecordsModule
{
    protected static $primary_key = 'ACTIVITYID';

    protected static $associated_entity = \Zoho\Crm\Entities\Note::class;

    protected static $supported_methods = [
        'getRecordById',
        'updateRecords',
        'deleteRecords',
        'getRelatedRecords',
        'getSearchRecordsByPDC',
    ];
}

<?php

namespace Zoho\CRM\Api\Modules;

class Calls extends AbstractRecordsModule
{
    protected static $primary_key = 'ACTIVITYID';

    protected static $supported_methods = [
        'getFields',
        'getRecordById',
        'getRecords',
        'getMyRecords',
        'searchRecords',
        'insertRecords',
        'updateRecords'
    ];
}

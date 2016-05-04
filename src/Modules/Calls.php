<?php

namespace Zoho\CRM\Modules;

class Calls extends AbstractRecordsModule
{
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

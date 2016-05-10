<?php

namespace Zoho\CRM\Api\Modules;

class Contacts extends AbstractRecordsModule
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

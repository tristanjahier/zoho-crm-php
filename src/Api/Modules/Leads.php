<?php

namespace Zoho\CRM\Api\Modules;

class Leads extends AbstractRecordsModule
{
    protected static $primary_key = 'LEADID';

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

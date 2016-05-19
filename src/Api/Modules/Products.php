<?php

namespace Zoho\CRM\Api\Modules;

class Products extends AbstractRecordsModule
{
    protected static $primary_key = 'PRODUCTID';

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

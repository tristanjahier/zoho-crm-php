<?php

namespace Zoho\Crm\Api\Modules;

class Leads extends AbstractRecordsModule
{
    protected static $primary_key = 'LEADID';

    protected static $associated_entity = \Zoho\Crm\Entities\Lead::class;

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

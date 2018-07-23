<?php

namespace Zoho\Crm\Api\Modules;

class Contacts extends AbstractRecordsModule
{
    protected static $primary_key = 'CONTACTID';

    protected static $associated_entity = \Zoho\Crm\Entities\Contact::class;

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

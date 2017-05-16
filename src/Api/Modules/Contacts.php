<?php

namespace Zoho\CRM\Api\Modules;

class Contacts extends AbstractRecordsModule
{
    protected static $primary_key = 'CONTACTID';

    protected static $associated_entity = \Zoho\CRM\Entities\Contact::class;

    protected static $supported_methods = [
        'getFields',
        'getRecordById',
        'getRecords',
        'getMyRecords',
        'searchRecords',
        'insertRecords',
        'updateRecords',
        'getDeletedRecordIds'
    ];
}

<?php

namespace Zoho\Crm\Api\Modules;

class Potentials extends AbstractRecordsModule
{
    protected static $primary_key = 'POTENTIALID';

    protected static $associated_entity = \Zoho\Crm\Entities\Potential::class;

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

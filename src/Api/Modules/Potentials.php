<?php

namespace Zoho\CRM\Api\Modules;

class Potentials extends AbstractRecordsModule
{
    protected static $primary_key = 'POTENTIALID';

    protected static $associated_entity = \Zoho\CRM\Entities\Potential::class;

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

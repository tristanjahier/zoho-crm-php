<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Potentials module handler.
 */
class Potentials extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $primary_key = 'POTENTIALID';

    /** @inheritdoc */
    protected static $associated_entity = \Zoho\Crm\Entities\Potential::class;

    /** @inheritdoc */
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

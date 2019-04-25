<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Calls module handler.
 *
 * @see https://www.zoho.com/crm/help/api/modules-fields.html#Calls
 */
class Calls extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $primary_key = 'ACTIVITYID';

    /** @inheritdoc */
    protected static $associated_entity = \Zoho\Crm\Entities\Call::class;

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
    ];
}

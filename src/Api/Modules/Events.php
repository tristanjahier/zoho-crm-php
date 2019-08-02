<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Events module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/modules-fields.html#Events
 */
class Events extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $primary_key = 'ACTIVITYID';

    /** @inheritdoc */
    protected static $associated_entity = \Zoho\Crm\Entities\Event::class;

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

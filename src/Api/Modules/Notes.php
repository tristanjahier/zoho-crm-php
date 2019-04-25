<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Notes module handler.
 */
class Notes extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $primary_key = 'ACTIVITYID';

    /** @inheritdoc */
    protected static $associated_entity = \Zoho\Crm\Entities\Note::class;

    /** @inheritdoc */
    protected static $supported_methods = [
        'getRecordById',
        'updateRecords',
        'deleteRecords',
        'getRelatedRecords',
        'getSearchRecordsByPDC',
    ];
}

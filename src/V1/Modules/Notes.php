<?php

namespace Zoho\Crm\V1\Modules;

/**
 * Notes module handler.
 */
class Notes extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\Entities\Note::class;

    /** @inheritdoc */
    protected static $supportedMethods = [
        'getRecordById',
        'updateRecords',
        'deleteRecords',
        'getRelatedRecords',
        'getSearchRecordsByPDC',
    ];
}

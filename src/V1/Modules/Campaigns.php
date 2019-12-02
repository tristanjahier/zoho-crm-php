<?php

namespace Zoho\Crm\V1\Modules;

/**
 * Campaigns module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/modules-fields.html#Campaigns
 */
class Campaigns extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\Entities\Records\Campaign::class;

    /** @inheritdoc */
    protected static $supportedMethods = [
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

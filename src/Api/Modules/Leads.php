<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Leads module handler.
 *
 * @see https://www.zoho.com/crm/help/api/modules-fields.html#Leads
 */
class Leads extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $primary_key = 'LEADID';

    /** @inheritdoc */
    protected static $associated_entity = \Zoho\Crm\Entities\Lead::class;

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

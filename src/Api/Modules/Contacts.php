<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Contacts module handler.
 *
 * @see https://www.zoho.com/crm/help/api/modules-fields.html#Contacts
 */
class Contacts extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $primary_key = 'CONTACTID';

    /** @inheritdoc */
    protected static $associated_entity = \Zoho\Crm\Entities\Contact::class;

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

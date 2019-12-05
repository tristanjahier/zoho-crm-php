<?php

namespace Zoho\Crm\V1\Modules;

/**
 * PurchaseOrders module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/modules-fields.html#Purchase_Order
 */
class PurchaseOrders extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\V1\Entities\Records\PurchaseOrder::class;

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

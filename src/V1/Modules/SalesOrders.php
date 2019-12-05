<?php

namespace Zoho\Crm\V1\Modules;

/**
 * SalesOrders module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/modules-fields.html#Sales_Order
 */
class SalesOrders extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\V1\Entities\Records\SaleOrder::class;

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

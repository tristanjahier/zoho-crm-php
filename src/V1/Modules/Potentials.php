<?php

namespace Zoho\Crm\V1\Modules;

/**
 * Potentials module handler.
 *
 * "Potentials" was the former name of the "Deals" module before June 2016.
 * Both modules refer to the same data, their usage is up to personal preference.
 */
class Potentials extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\Entities\Records\Potential::class;

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

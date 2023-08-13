<?php

namespace Zoho\Crm\V1\Modules;

/**
 * ContactRoles module handler.
 */
class ContactRoles extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\V1\Entities\ContactRole::class;

    /** @inheritdoc */
    protected static $supportedMethods = [
        'getRelatedRecords',
    ];
}

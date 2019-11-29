<?php

namespace Zoho\Crm\Modules;

/**
 * ContactRoles module handler.
 */
class ContactRoles extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\Entities\ContactRole::class;

    /** @inheritdoc */
    protected static $supportedMethods = [
        'getRelatedRecords',
    ];
}

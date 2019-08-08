<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Attachments module handler.
 */
class Attachments extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\Entities\Attachment::class;

    /** @inheritdoc */
    protected static $supportedMethods = [
        'getRelatedRecords',
    ];
}

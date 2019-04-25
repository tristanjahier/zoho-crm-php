<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Attachments module handler.
 */
class Attachments extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $primary_key = 'id';

    /** @inheritdoc */
    protected static $associated_entity = \Zoho\Crm\Entities\Attachment::class;

    /** @inheritdoc */
    protected static $supported_methods = [
        'getRelatedRecords',
    ];
}

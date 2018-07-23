<?php

namespace Zoho\Crm\Api\Modules;

class Attachments extends AbstractRecordsModule
{
    protected static $primary_key = 'id';

    protected static $associated_entity = \Zoho\Crm\Entities\Attachment::class;

    protected static $supported_methods = [
        'getRelatedRecords',
    ];
}

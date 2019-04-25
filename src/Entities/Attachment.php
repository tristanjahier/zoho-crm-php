<?php

namespace Zoho\Crm\Entities;

/**
 * Attachment entity class.
 */
class Attachment extends AbstractEntity
{
    /** @inheritdoc */
    protected static $property_aliases = [
        'id'          => 'id',
        'owner'       => 'SMOWNERID',
        'owner_name'  => 'Attached By',
        'file_name'   => 'File Name',
        'file_size'   => 'Size',
        'attached_at' => 'Modified Time',
    ];
}

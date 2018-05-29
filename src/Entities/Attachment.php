<?php

namespace Zoho\CRM\Entities;

class Attachment extends AbstractEntity
{
    protected static $property_aliases = [
        'id'          => 'id',
        'owner'       => 'SMOWNERID',
        'owner_name'  => 'Attached By',
        'file_name'   => 'File Name',
        'file_size'   => 'Size',
        'attached_at' => 'Modified Time',
    ];
}

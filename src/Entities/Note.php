<?php

namespace Zoho\CRM\Entities;

class Note extends AbstractEntity
{
    protected static $property_aliases = [
        'id'               => 'ACTIVITYID',
        'owner'            => 'SMOWNERID',
        'owner_name'       => 'Owner Name',
        'title'            => 'Title',
        'content'          => 'Note Content',
        'is_voice'         => 'ISVOICENOTES',
        'created_by'       => 'SMCREATORID',
        'created_by_name'  => 'Created By',
        'modified_by'      => 'MODIFIEDBY',
        'modified_by_name' => 'Modified By',
        'created_at'       => 'Created Time',
        'modified_at'      => 'Modified Time',
    ];
}

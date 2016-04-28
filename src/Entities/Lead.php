<?php

namespace Zoho\CRM\Entities;

class Lead extends AbstractEntity
{
    protected static $properties_mapping = [
        'id'               => 'LEADID',
        'owner'            => 'SMOWNERID',
        'owner_name'       => 'Lead Owner',
        'title'            => 'Salutation',
        'first_name'       => 'First Name',
        'last_name'        => 'Last Name',
        'email'            => 'Email',
        'phone'            => 'Phone',
        'source'           => 'Lead Source',
        'created_by'       => 'SMCREATORID',
        'created_by_name'  => 'Created By',
        'modified_by'      => 'MODIFIEDBY',
        'modified_by_name' => 'Modified By',
        'created_on'       => 'Created Time',
        'modified_on'      => 'Modified Time',
        'last_activity_on' => 'Last Activity Time'
    ];
}

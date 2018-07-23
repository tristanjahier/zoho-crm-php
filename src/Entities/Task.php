<?php

namespace Zoho\Crm\Entities;

class Task extends AbstractEntity
{
    protected static $property_aliases = [
        'id'                 => 'ACTIVITYID',
        'owner'              => 'SMOWNERID',
        'owner_name'         => 'Task Owner',
        'subject'            => 'Subject',
        'description'        => 'Description',
        'due_on'             => 'Due Date',
        'status'             => 'Status',
        'priority'           => 'Priority',
        'notification_email' => 'Send Notification Email',
        'closed_at'          => 'Closed Time',
        'recurrence'         => 'Recurring Activity',
        'tags'               => 'Tag',
        'related_to'         => 'RELATEDTOID',
        'related_to_module'  => 'SEMODULE',
        'related_to_name'    => 'Related To',
        'created_by'         => 'SMCREATORID',
        'created_by_name'    => 'Created By',
        'modified_by'        => 'MODIFIEDBY',
        'modified_by_name'   => 'Modified By',
        'created_at'         => 'Created Time',
        'modified_at'        => 'Modified Time',
    ];
}

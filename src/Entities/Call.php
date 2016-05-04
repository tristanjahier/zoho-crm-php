<?php

namespace Zoho\CRM\Entities;

class Call extends AbstractEntity
{
    protected static $properties_mapping = [
        'id'               => 'ACTIVITYID',
        'owner'            => 'SMOWNERID',
        'owner_name'       => 'Call Owner',
        'subject'          => 'Subject',
        'type'             => 'Call Type',
        'purpose'          => 'Call Purpose',
        'who_id'           => 'Who Id',
        'started_on'       => 'Call Start Time',
        'duration'         => 'Call Duration',
        'duration_seconds' => 'Call Duration (in seconds)',
        'description'      => 'Description',
        'result'           => 'Call Result',
        'reminder'         => 'Reminder',
        'created_by'       => 'SMCREATORID',
        'created_by_name'  => 'Created By',
        'modified_by'      => 'MODIFIEDBY',
        'modified_by_name' => 'Modified By',
        'created_on'       => 'Created Time',
        'modified_on'      => 'Modified Time',
    ];
}

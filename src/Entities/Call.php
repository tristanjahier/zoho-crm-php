<?php

namespace Zoho\Crm\Entities;

/**
 * Call entity class.
 */
class Call extends AbstractEntity
{
    /** @inheritdoc */
    protected static $property_aliases = [
        'id'               => 'ACTIVITYID',
        'owner'            => 'SMOWNERID',
        'owner_name'       => 'Call Owner',
        'subject'          => 'Subject',
        'type'             => 'Call Type',
        'purpose'          => 'Call Purpose',
        'who_id'           => 'Who Id',
        'started_at'       => 'Call Start Time',
        'duration'         => 'Call Duration',
        'duration_seconds' => 'Call Duration (in seconds)',
        'description'      => 'Description',
        'result'           => 'Call Result',
        'reminder'         => 'Reminder',
        'created_by'       => 'SMCREATORID',
        'created_by_name'  => 'Created By',
        'modified_by'      => 'MODIFIEDBY',
        'modified_by_name' => 'Modified By',
        'created_at'       => 'Created Time',
        'modified_at'      => 'Modified Time',
    ];
}

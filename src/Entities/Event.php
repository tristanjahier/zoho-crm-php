<?php

namespace Zoho\Crm\Entities;

/**
 * Event entity class.
 */
class Event extends AbstractEntity
{
    /** @inheritdoc */
    protected static $propertyAliases = [
        'id'                => 'ACTIVITYID',
        'uid'               => 'UID',
        'owner'             => 'SMOWNERID',
        'owner_name'        => 'Event Owner',
        'subject'           => 'Subject',
        'description'       => 'Description',
        'location'          => 'Venue',
        'starts_at'         => 'Start DateTime',
        'ends_at'           => 'End DateTime',
        'recurrence_id'     => 'RECURRENCEID',
        'recurrence'        => 'Recurring Activity',
        'tags'              => 'Tag',
        'related_to'        => 'RELATEDTOID',
        'related_to_module' => 'SEMODULE',
        'related_to_name'   => 'Related To',
        'created_by'        => 'SMCREATORID',
        'created_by_name'   => 'Created By',
        'modified_by'       => 'MODIFIEDBY',
        'modified_by_name'  => 'Modified By',
        'created_at'        => 'Created Time',
        'modified_at'       => 'Modified Time',
    ];
}

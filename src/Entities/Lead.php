<?php

namespace Zoho\Crm\Entities;

/**
 * Lead entity class.
 */
class Lead extends AbstractEntity
{
    /** @inheritdoc */
    protected static $propertyAliases = [
        'id'                  => 'LEADID',
        'owner'               => 'SMOWNERID',
        'owner_name'          => 'Lead Owner',
        'title'               => 'Salutation',
        'first_name'          => 'First Name',
        'last_name'           => 'Last Name',
        'source'              => 'Lead Source',
        'email'               => 'Email',
        'phone'               => 'Phone',
        'mobile'              => 'Mobile',
        'street'              => 'Street',
        'city'                => 'City',
        'state'               => 'State',
        'zipcode'             => 'Zip Code',
        'description'         => 'Description',
        'status'              => 'Lead Status',
        'average_time_spent'  => 'Average Time Spent (Minutes)',
        'chat_messages_count' => 'Number Of Chats',
        'days_visited'        => 'Days Visited',
        'last_visited_at'     => 'Last Visited Time',
        'first_visited_at'    => 'First Visited Time',
        'first_visited_url'   => 'First Visited URL',
        'visitor_score'       => 'Visitor Score',
        'referrer'            => 'Referrer',
        'created_by'          => 'SMCREATORID',
        'created_by_name'     => 'Created By',
        'modified_by'         => 'MODIFIEDBY',
        'modified_by_name'    => 'Modified By',
        'created_at'          => 'Created Time',
        'modified_at'         => 'Modified Time',
        'last_activity_at'    => 'Last Activity Time'
    ];
}

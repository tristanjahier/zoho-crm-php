<?php

namespace Zoho\CRM\Entities;

class Lead extends AbstractEntity
{
    protected static $properties_mapping = [
        'id'                  => 'LEADID',
        'owner'               => 'SMOWNERID',
        'owner_name'          => 'Lead Owner',
        'email'               => 'Email',
        'title'               => 'Salutation',
        'first_name'          => 'First Name',
        'last_name'           => 'Last Name',
        'source'              => 'Lead Source',
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
        'last_visited_on'     => 'Last Visited Time',
        'first_visited_on'    => 'First Visited Time',
        'first_visited_url'   => 'First Visited URL',
        'visitor_score'       => 'Visitor Score',
        'referrer'            => 'Referrer',
        'created_by'          => 'SMCREATORID',
        'created_by_name'     => 'Created By',
        'modified_by'         => 'MODIFIEDBY',
        'modified_by_name'    => 'Modified By',
        'created_on'          => 'Created Time',
        'modified_on'         => 'Modified Time',
        'last_activity_on'    => 'Last Activity Time'
    ];
}

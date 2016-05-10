<?php

namespace Zoho\CRM\Entities;

class Contact extends AbstractEntity
{
    protected static $properties_mapping = [
        'id'                  => 'CONTACTID',
        'owner'               => 'SMOWNERID',
        'owner_name'          => 'Contact Owner',
        'title'               => 'Salutation',
        'first_name'          => 'First Name',
        'last_name'           => 'Last Name',
        'lead_source'         => 'Lead Source',
        'email'               => 'Email',
        'fax'                 => 'Fax',
        'phone'               => 'Phone',
        'other_phone'         => 'Other Phone',
        'mailing_street'      => 'Mailing Street',
        'mailing_zipcode'     => 'Mailing Zip',
        'mailing_city'        => 'Mailing City',
        'mailing_state'       => 'Mailing State',
        'email_opt_out'       => 'Email Opt Out',
        'description'         => 'Description',
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

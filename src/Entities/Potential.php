<?php

namespace Zoho\CRM\Entities;

class Potential extends AbstractEntity
{
    protected static $properties_mapping = [
        'id'               => 'POTENTIALID',
        'owner'            => 'SMOWNERID',
        'owner_name'       => 'Potential Owner',
        'name'             => 'Potential Name',
        'amount'           => 'Amount',
        'closing_date'     => 'Closing Date',
        'contact'          => 'CONTACTID',
        'contact_name'     => 'Contact Name',
        'stage'            => 'Stage',
        'probability'      => 'Probability',
        'expected_revenue' => 'Expected Revenue',
        'territory'        => 'Territory',
        'lead_source'      => 'Lead Source',
        'description'      => 'Description',
        'created_by'       => 'SMCREATORID',
        'created_by_name'  => 'Created By',
        'modified_by'      => 'MODIFIEDBY',
        'modified_by_name' => 'Modified By',
        'created_at'       => 'Created Time',
        'modified_at'      => 'Modified Time',
        'last_activity_at' => 'Last Activity Time'
    ];
}

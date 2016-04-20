<?php

namespace Zoho\CRM\Entities;

class User extends AbstractEntity
{
    protected static $properties_mapping = [
        'id'           => 'id',
        'zipcode'      => 'zip',
        'phone'        => 'phone',
        'fax'          => 'fax',
        'status'       => 'status',
        'website'      => 'website',
        'street'       => 'street',
        'state'        => 'state',
        'city'         => 'city',
        'country'      => 'country',
        'full_name'    => 'content',
        'timezone'     => 'timezone',
        'zuid'         => 'zuid',
        'email'        => 'email',
        'role'         => 'role',
        'language'     => 'language',
        'confirmed'    => 'confirm',
        'profile'      => 'profile',
        'mobile_phone' => 'mobile',
    ];
}

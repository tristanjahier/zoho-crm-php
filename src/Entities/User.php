<?php

namespace Zoho\CRM\Entities;

class User extends AbstractEntity
{
    protected static $properties_mapping = [
        'id'       => 'id',
        'zip'      => 'zip',
        'phone'    => 'phone',
        'fax'      => 'fax',
        'status'   => 'status',
        'website'  => 'website',
        'street'   => 'street',
        'state'    => 'state',
        'city'     => 'city',
        'country'  => 'country',
        'content'  => 'content',
        'timezone' => 'timezone',
        'zuid'     => 'zuid',
        'email'    => 'email',
        'role'     => 'role',
        'language' => 'language',
        'confirm'  => 'confirm',
        'profile'  => 'profile',
        'mobile'   => 'mobile',
    ];
}

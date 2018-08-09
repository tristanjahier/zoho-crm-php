<?php

namespace Zoho\Crm\Api\Modules;

class Users extends AbstractModule
{
    protected static $associated_entity = \Zoho\Crm\Entities\User::class;

    protected static $supported_methods = [
        'getUsers'
    ];

    public function all()
    {
        return $this->newQuery('getUsers', ['type' => 'AllUsers']);
    }

    public function active()
    {
        return $this->newQuery('getUsers', ['type' => 'ActiveUsers']);
    }

    public function inactive()
    {
        return $this->newQuery('getUsers', ['type' => 'DeactiveUsers']);
    }

    public function admins()
    {
        return $this->newQuery('getUsers', ['type' => 'AdminUsers']);
    }

    public function activeConfirmedAdmins()
    {
        return $this->newQuery('getUsers', ['type' => 'ActiveConfirmedAdmins']);
    }
}

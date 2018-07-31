<?php

namespace Zoho\Crm\Api\Modules;

class Users extends AbstractModule
{
    protected static $associated_entity = \Zoho\Crm\Entities\User::class;

    protected static $supported_methods = [
        'getUsers'
    ];

    public function getAll()
    {
        return $this->newQuery('getUsers', ['type' => 'AllUsers'])->get();
    }

    public function getActives()
    {
        return $this->newQuery('getUsers', ['type' => 'ActiveUsers'])->get();
    }

    public function getInactives()
    {
        return $this->newQuery('getUsers', ['type' => 'DeactiveUsers'])->get();
    }

    public function getAdmins()
    {
        return $this->newQuery('getUsers', ['type' => 'AdminUsers'])->get();
    }

    public function getActiveConfirmedAdmins()
    {
        return $this->newQuery('getUsers', ['type' => 'ActiveConfirmedAdmins'])->get();
    }
}

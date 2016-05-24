<?php

namespace Zoho\CRM\Api\Modules;

class Users extends AbstractModule
{
    protected static $associated_entity = \Zoho\CRM\Entities\User::class;

    protected static $supported_methods = [
        'getUsers'
    ];

    public function getAll()
    {
        return $this->request('getUsers', ['type' => 'AllUsers']);
    }

    public function getActives()
    {
        return $this->request('getUsers', ['type' => 'ActiveUsers']);
    }

    public function getInactives()
    {
        return $this->request('getUsers', ['type' => 'DeactiveUsers']);
    }

    public function getAdmins()
    {
        return $this->request('getUsers', ['type' => 'AdminUsers']);
    }

    public function getActiveConfirmedAdmins()
    {
        return $this->request('getUsers', ['type' => 'ActiveConfirmedAdmins']);
    }
}

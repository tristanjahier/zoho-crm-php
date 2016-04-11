<?php

namespace Zoho\CRM\Modules;

class Users extends AbstractModule
{
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

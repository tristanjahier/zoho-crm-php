<?php

namespace Zoho\Crm\V1\Modules;

/**
 * Users module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/getusers.html
 */
class Users extends AbstractModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\V1\Entities\User::class;

    /** @inheritdoc */
    protected static $supportedMethods = [
        'getUsers'
    ];

    /**
     * Create a query to get all users.
     *
     * @return \Zoho\Crm\V1\Query
     */
    public function all()
    {
        return $this->newQuery('getUsers', ['type' => 'AllUsers']);
    }

    /**
     * Create a query to get active users.
     *
     * @return \Zoho\Crm\V1\Query
     */
    public function active()
    {
        return $this->newQuery('getUsers', ['type' => 'ActiveUsers']);
    }

    /**
     * Create a query to get inactive/deactivated users.
     *
     * @return \Zoho\Crm\V1\Query
     */
    public function inactive()
    {
        return $this->newQuery('getUsers', ['type' => 'DeactiveUsers']);
    }

    /**
     * Create a query to get administrators.
     *
     * @return \Zoho\Crm\V1\Query
     */
    public function admins()
    {
        return $this->newQuery('getUsers', ['type' => 'AdminUsers']);
    }

    /**
     * Create a query to get active and confirmed administrators.
     *
     * @return \Zoho\Crm\V1\Query
     */
    public function activeConfirmedAdmins()
    {
        return $this->newQuery('getUsers', ['type' => 'ActiveConfirmedAdmins']);
    }
}

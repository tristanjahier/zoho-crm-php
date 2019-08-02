<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Users module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/getusers.html
 */
class Users extends AbstractModule
{
    /** @var string The name of the identifier field */
    protected static $primaryKey = 'id';

    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\Entities\User::class;

    /** @inheritdoc */
    protected static $supportedMethods = [
        'getUsers'
    ];

    /**
     * Get the primary key / the identifier field of the module.
     *
     * @return string
     */
    public static function primaryKey()
    {
        return static::$primaryKey;
    }

    /**
     * Create a query to get all users.
     *
     * @return \Zoho\Crm\Api\Query
     */
    public function all()
    {
        return $this->newQuery('getUsers', ['type' => 'AllUsers']);
    }

    /**
     * Create a query to get active users.
     *
     * @return \Zoho\Crm\Api\Query
     */
    public function active()
    {
        return $this->newQuery('getUsers', ['type' => 'ActiveUsers']);
    }

    /**
     * Create a query to get inactive/deactivated users.
     *
     * @return \Zoho\Crm\Api\Query
     */
    public function inactive()
    {
        return $this->newQuery('getUsers', ['type' => 'DeactiveUsers']);
    }

    /**
     * Create a query to get administrators.
     *
     * @return \Zoho\Crm\Api\Query
     */
    public function admins()
    {
        return $this->newQuery('getUsers', ['type' => 'AdminUsers']);
    }

    /**
     * Create a query to get active and confirmed administrators.
     *
     * @return \Zoho\Crm\Api\Query
     */
    public function activeConfirmedAdmins()
    {
        return $this->newQuery('getUsers', ['type' => 'ActiveConfirmedAdmins']);
    }
}

<?php

namespace Zoho\Crm\V2;

/**
 * Static helper class to manipulate and validate OAuth authorization scopes.
 *
 * @link https://www.zoho.com/crm//developer/docs/api/oauth-overview.html#scopes
 */
final class Scopes
{
    /** @var string The root of each Zoho CRM scopes */
    const SERVICE_NAME = 'ZohoCRM';

    /** @var array All valid scopes */
    const SCOPES = [
        'users' => ['all', 'read', 'write', 'create', 'update', 'delete'],

        'org' => ['all', 'read', 'write', 'create', 'update', 'delete'],

        'settings' => [
            'all', 'read', 'write', 'create', 'update', 'delete',

            'territories'    => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'custom_views'   => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'related_lists'  => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'modules'        => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'variables'      => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'tags'           => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'tab_groups'     => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'fields'         => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'layouts'        => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'macros'         => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'custom_links'   => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'custom_buttons' => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'roles'          => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'profiles'       => ['all', 'read', 'write', 'create', 'update', 'delete'],
        ],

        'modules' => [
            'all', 'read', 'write', 'create', 'update', 'delete',

            'approvals'      => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'leads'          => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'accounts'       => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'contacts'       => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'deals'          => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'campaigns'      => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'tasks'          => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'cases'          => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'events'         => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'calls'          => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'solutions'      => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'products'       => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'vendors'        => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'pricebooks'     => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'quotes'         => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'salesorders'    => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'purchaseorders' => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'invoices'       => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'custom'         => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'dashboards'     => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'notes'          => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'activities'     => ['all', 'read', 'write', 'create', 'update', 'delete'],
            'search'         => ['all', 'read', 'write', 'create', 'update', 'delete'],
        ],

        'bulk' => ['all', 'read', 'write', 'create', 'update', 'delete'],

        'notifications' => ['all', 'read', 'write', 'create', 'update', 'delete'],

        'coql' => ['read'],
    ];

    /**
     * The constructor.
     *
     * It is private to prevent instanciation.
     */
    private function __construct()
    {
        //
    }

    /**
     * Generate a string with all available scopes, with full access.
     *
     * @return string
     */
    public static function getAll(): string
    {
        $scopes = [];

        foreach (self::SCOPES as $scope => $subScopes) {
            // Edge cases
            if ($scope == 'coql') {
                $operationType = 'read';
            } else {
                $operationType = 'all';
            }

            $scopes[] = self::SERVICE_NAME . '.' . $scope . '.' . $operationType;
        }

        return join(',', $scopes);
    }

    /**
     * Generate a string with all available scopes, with read-only access.
     *
     * @return string
     */
    public static function getAllReadOnly(): string
    {
        $scopes = [];

        foreach (self::SCOPES as $scope => $subScopes) {
            if (in_array('read', $subScopes)) {
                $scopes[] = self::SERVICE_NAME . '.' . $scope . '.read';
            }
        }

        return join(',', $scopes);
    }

    /**
     * Generate a string with given modules' scopes.
     *
     * @param string[] $modules (optional) The wanted modules. Empty array = all modules.
     * @param bool $readOnly (optional) Whether to get read-only access or not
     * @return string
     */
    public static function getModules(array $modules = [], bool $readOnly = false): string
    {
        if (empty($modules)) {
            return self::SERVICE_NAME . '.modules.' . ($readOnly ? 'read' : 'all');
        }

        $scopes = [];

        foreach ($modules as $module) {
            $operationType = $readOnly ? 'read' : 'all';
            $scopes[] = self::SERVICE_NAME . '.modules.' . $module . '.' . $operationType;
        }

        return join(',', $scopes);
    }
}

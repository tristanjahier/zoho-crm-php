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
        'users' => ['all', 'read'],

        'org' => ['all', 'read'],

        'settings' => [
            'all',
            'read',

            'territories',
            'custom_views',
            'related_lists',
            'modules',
            'variables',
            'tags',
            'tab_groups',
            'fields',
            'layouts',
            'macros',
            'custom_links',
            'custom_buttons',
            'roles',
            'profiles',
        ],

        'modules' => [
            'all',
            'read',

            'approvals',
            'leads',
            'accounts',
            'contacts',
            'deals',
            'campaigns',
            'tasks',
            'cases',
            'events',
            'calls',
            'solutions',
            'products',
            'vendors',
            'pricebooks',
            'quotes',
            'salesorders',
            'purchaseorders',
            'invoices',
            'custom',
            'dashboards',
            'notes',
            'activities',
            'search',
        ],

        'bulk' => ['all', 'read', 'create'],

        'notifications' => ['read', 'create', 'update', 'delete'],

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

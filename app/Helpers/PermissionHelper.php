<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Check if user has permission
     */
    public static function can(string $permission): bool
    {
        return Auth::check() && Auth::user()->can($permission);
    }

    /**
     * Check if user has any of the given permissions
     */
    public static function canAny(array $permissions): bool
    {
        if (!Auth::check()) {
            return false;
        }

        foreach ($permissions as $permission) {
            if (Auth::user()->can($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public static function canAll(array $permissions): bool
    {
        if (!Auth::check()) {
            return false;
        }

        foreach ($permissions as $permission) {
            if (!Auth::user()->can($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has role
     */
    public static function hasRole(string $role): bool
    {
        return Auth::check() && Auth::user()->hasRole($role);
    }

    /**
     * Check if user has any of the given roles
     */
    public static function hasAnyRole(array $roles): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole($roles);
    }

    /**
     * Check if user can access all data (no unit scope restriction)
     */
    public static function canAccessAllData(): bool
    {
        return self::hasAnyRole(['super-admin', 'admin', 'bendahara-pengeluaran', 'sekretariat']);
    }

    /**
     * Check if user can manage documents
     */
    public static function canManageDocuments(): bool
    {
        return self::canAny([
            'documents.create',
            'documents.edit',
            'documents.delete',
            'documents.approve'
        ]);
    }

    /**
     * Check if user can view rekapitulasi
     */
    public static function canViewRekap(): bool
    {
        return self::can('rekap.view');
    }

    /**
     * Check if user can export rekapitulasi
     */
    public static function canExportRekap(): bool
    {
        return self::can('rekap.export');
    }

    /**
     * Get user's unit ID for scope filtering
     */
    public static function getUserUnitId(): ?int
    {
        if (!Auth::check()) {
            return null;
        }

        // If user can access all data, return null (no filtering)
        if (self::canAccessAllData()) {
            return null;
        }

        // Return user's unit ID for scope filtering
        return Auth::user()->unit_id;
    }

    /**
     * Get user's role name
     */
    public static function getUserRoleName(): ?string
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::user()->getRoleNames()->first();
    }

    /**
     * Get user's role display name
     */
    public static function getUserRoleDisplayName(): ?string
    {
        $roleName = self::getUserRoleName();
        
        if (!$roleName) {
            return null;
        }

        $roleDisplayNames = [
            'super-admin' => 'Super Admin',
            'admin' => 'Admin',
            'bendahara-pengeluaran' => 'Bendahara Pengeluaran',
            'bendahara-pengeluaran-pembantu' => 'Bendahara Pengeluaran Pembantu',
            'sekretariat' => 'Sekretariat',
        ];

        return $roleDisplayNames[$roleName] ?? ucfirst(str_replace('-', ' ', $roleName));
    }

    /**
     * Check if user is super admin
     */
    public static function isSuperAdmin(): bool
    {
        return self::hasRole('super-admin');
    }

    /**
     * Check if user can manage users (admin or super-admin)
     */
    public static function canManageUsers(): bool
    {
        return self::hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * Check if user can manage permissions (only super-admin)
     */
    public static function canManagePermissions(): bool
    {
        return self::hasRole('super-admin');
    }

    /**
     * Check if user can manage user roles (admin and super-admin)
     */
    public static function canManageUserRoles(): bool
    {
        return self::hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * Check if user can manage master data
     */
    public static function canManageMasterData(): bool
    {
        return self::canAny([
            'master-data.create',
            'master-data.edit',
            'master-data.delete'
        ]);
    }

    /**
     * Check if user can manage reference rates
     */
    public static function canManageReferenceRates(): bool
    {
        return self::canAny([
            'reference-rates.create',
            'reference-rates.edit',
            'reference-rates.delete'
        ]);
    }

    /**
     * Check if user can manage locations
     */
    public static function canManageLocations(): bool
    {
        return self::canAny([
            'locations.create',
            'locations.edit',
            'locations.delete'
        ]);
    }

    /**
     * Get user's permission summary for display
     */
    public static function getUserPermissionSummary(): array
    {
        if (!Auth::check()) {
            return [];
        }

        $user = Auth::user();
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();

        return [
            'system' => [
                'manage_users' => in_array('system.manage-users', $permissions),
                'manage_permissions' => in_array('system.manage-permissions', $permissions),
                'access_all_data' => in_array('system.access-all-data', $permissions),
            ],
            'master_data' => [
                'view' => in_array('master-data.view', $permissions),
                'create' => in_array('master-data.create', $permissions),
                'edit' => in_array('master-data.edit', $permissions),
                'delete' => in_array('master-data.delete', $permissions),
            ],
            'users' => [
                'view' => in_array('users.view', $permissions),
                'create' => in_array('users.create', $permissions),
                'edit' => in_array('users.edit', $permissions),
                'delete' => in_array('users.delete', $permissions),
            ],
            'documents' => [
                'view' => in_array('documents.view', $permissions),
                'create' => in_array('documents.create', $permissions),
                'edit' => in_array('documents.edit', $permissions),
                'delete' => in_array('documents.delete', $permissions),
                'approve' => in_array('documents.approve', $permissions),
            ],
            'rekap' => [
                'view' => in_array('rekap.view', $permissions),
                'export' => in_array('rekap.export', $permissions),
            ],
            'reference_rates' => [
                'view' => in_array('reference-rates.view', $permissions),
                'create' => in_array('reference-rates.create', $permissions),
                'edit' => in_array('reference-rates.edit', $permissions),
                'delete' => in_array('reference-rates.delete', $permissions),
            ],
            'locations' => [
                'view' => in_array('locations.view', $permissions),
                'create' => in_array('locations.create', $permissions),
                'edit' => in_array('locations.edit', $permissions),
                'delete' => in_array('locations.delete', $permissions),
            ],
        ];
    }
}

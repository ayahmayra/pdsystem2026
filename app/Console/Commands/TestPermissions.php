<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Helpers\PermissionHelper;

class TestPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:test {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test permissions for a specific user or all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        if ($userId) {
            $this->testUserPermissions($userId);
        } else {
            $this->testAllUsersPermissions();
        }
    }

    private function testUserPermissions($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return;
        }

        $this->info("Testing permissions for user: {$user->name} ({$user->email})");
        $this->line("Unit: " . ($user->unit->name ?? 'No unit'));
        $this->line("Roles: " . $user->getRoleNames()->implode(', '));
        $this->line("");

        // Test permissions
        $permissions = [
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.delete',
            'nota-dinas.view',
            'nota-dinas.create',
            'nota-dinas.edit',
            'nota-dinas.delete',
            'spt.view',
            'spt.create',
            'spt.edit',
            'spt.delete',
            'sppd.view',
            'sppd.create',
            'sppd.edit',
            'sppd.delete',
            'receipts.view',
            'receipts.create',
            'receipts.edit',
            'receipts.delete',
            'rekap.view',
            'rekap.export',
            'master-data.view',
            'master-data.create',
            'master-data.edit',
            'master-data.delete',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'reference-rates.view',
            'reference-rates.create',
            'reference-rates.edit',
            'reference-rates.delete',
        ];

        $this->info("Permission Test Results:");
        $this->line("======================");

        foreach ($permissions as $permission) {
            $hasPermission = $user->can($permission);
            $status = $hasPermission ? '✅' : '❌';
            $this->line("{$status} {$permission}");
        }

        $this->line("");
        $this->info("Helper Method Tests (using specific user):");
        $this->line("=========================================");
        
        // Simulate login for helper methods
        auth()->login($user);
        
        $this->line("Can Access All Data: " . (PermissionHelper::canAccessAllData() ? '✅ Yes' : '❌ No'));
        $this->line("Can Manage Documents: " . (PermissionHelper::canManageDocuments() ? '✅ Yes' : '❌ No'));
        $this->line("Can View Rekap: " . (PermissionHelper::canViewRekap() ? '✅ Yes' : '❌ No'));
        $this->line("Can Export Rekap: " . (PermissionHelper::canExportRekap() ? '✅ Yes' : '❌ No'));
        $this->line("User Unit ID: " . (PermissionHelper::getUserUnitId() ?? 'No restriction'));
        $this->line("Role Display Name: " . (PermissionHelper::getUserRoleDisplayName() ?? 'No role'));
        
        // Logout
        auth()->logout();
    }

    private function testAllUsersPermissions()
    {
        $users = User::with(['unit', 'roles'])->get();
        
        $this->info("Testing permissions for all users:");
        $this->line("==================================");
        $this->line("");

        foreach ($users as $user) {
            $this->line("User: {$user->name} ({$user->email})");
            $this->line("Unit: " . ($user->unit->name ?? 'No unit'));
            $this->line("Roles: " . $user->getRoleNames()->implode(', '));
            
            // Test key permissions
            $keyPermissions = [
                'documents.view',
                'nota-dinas.create',
                'rekap.view',
                'master-data.view',
            ];
            
            $permissionResults = [];
            foreach ($keyPermissions as $permission) {
                $permissionResults[] = $user->can($permission) ? '✅' : '❌';
            }
            
            $this->line("Key Permissions: " . implode(' ', $permissionResults));
            $this->line("Can Access All Data: " . (PermissionHelper::canAccessAllData() ? '✅' : '❌'));
            $this->line("---");
        }
    }
}
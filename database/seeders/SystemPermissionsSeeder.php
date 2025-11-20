<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SystemPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // System Management Permissions
        $systemPermissions = [
            'system.manage-users',
            'system.manage-permissions',
            'system.access-all-data',
        ];

        foreach ($systemPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('System permissions created successfully.');
    }
}

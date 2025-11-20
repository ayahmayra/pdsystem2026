<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MenuPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menu Access Permissions
        $menuPermissions = [
            'menu.dashboard',
            'menu.documents',
            'menu.master-data',
            'menu.location-routes',
            'menu.reference-rates',
            'menu.rekap',
            'menu.configuration',
            'menu.organization',
            'menu.ranks',
            'menu.doc-number-formats',
            'menu.number-sequences',
            'menu.document-numbers',
            // Location & Routes submenu permissions
            'menu.provinces',
            'menu.cities',
            'menu.districts',
            'menu.org-places',
            'menu.transport-modes',
            'menu.travel-routes',
        ];

        foreach ($menuPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('Menu permissions created successfully.');
    }
}

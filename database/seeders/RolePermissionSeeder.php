<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Menu Access Permissions
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
            'menu.provinces',
            'menu.cities',
            'menu.districts',
            'menu.org-places',
            'menu.transport-modes',
            'menu.travel-routes',
            
            // Master Data Management
            'master-data.view',
            'master-data.create',
            'master-data.edit',
            'master-data.delete',
            
            // User Management (except super admin)
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Document Management
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.delete',
            'documents.approve',
            
            // Nota Dinas
            'nota-dinas.view',
            'nota-dinas.create',
            'nota-dinas.edit',
            'nota-dinas.delete',
            'nota-dinas.approve',
            
            // SPT (Surat Perjalanan Tugas)
            'spt.view',
            'spt.create',
            'spt.edit',
            'spt.delete',
            'spt.approve',
            
            // SPPD (Surat Perintah Perjalanan Dinas)
            'sppd.view',
            'sppd.create',
            'sppd.edit',
            'sppd.delete',
            'sppd.approve',
            
            // Receipts (Kwitansi)
            'receipts.view',
            'receipts.create',
            'receipts.edit',
            'receipts.delete',
            'receipts.approve',
            
            // Trip Reports (Laporan Perjalanan)
            'trip-reports.view',
            'trip-reports.create',
            'trip-reports.edit',
            'trip-reports.delete',
            'trip-reports.approve',
            
            // Rekapitulasi
            'rekap.view',
            'rekap.export',
            
            // Reference Rates
            'reference-rates.view',
            'reference-rates.create',
            'reference-rates.edit',
            'reference-rates.delete',
            
            // Location & Routes
            'locations.view',
            'locations.create',
            'locations.edit',
            'locations.delete',
            
            // All Access (for super admin)
            'all-access',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // 1. Super Admin - dapat mengakses semua fitur dan data
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Admin - mengelola master data, hak akses user (kecuali superadmin), referensi lokasi dan rute, referensi tarif, dan menu konfigurasi
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            // Menu Access
            'menu.dashboard',
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
            'menu.provinces',
            'menu.cities',
            'menu.districts',
            'menu.org-places',
            'menu.transport-modes',
            'menu.travel-routes',
            // Master Data
            'master-data.view',
            'master-data.create',
            'master-data.edit',
            'master-data.delete',
            // User Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            // Reference Rates
            'reference-rates.view',
            'reference-rates.create',
            'reference-rates.edit',
            'reference-rates.delete',
            // Location & Routes
            'locations.view',
            'locations.create',
            'locations.edit',
            'locations.delete',
            // Rekapitulasi
            'rekap.view',
            'rekap.export',
        ]);

        // 3. Bendahara Pengeluaran - mengelola semua dokumen tanpa scope bidang, dapat mengakses semua fitur rekapitulasi dan master data
        $bendaharaPengeluaran = Role::firstOrCreate(['name' => 'bendahara-pengeluaran']);
        $bendaharaPengeluaran->syncPermissions([
            // Menu Access
            'menu.dashboard',
            'menu.documents',
            'menu.master-data',
            'menu.rekap',
            // Master Data
            'master-data.view',
            'master-data.create',
            'master-data.edit',
            'users.view',
            'users.create',
            'users.edit',
            // Documents
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.delete',
            'documents.approve',
            'nota-dinas.view',
            'nota-dinas.create',
            'nota-dinas.edit',
            'nota-dinas.delete',
            'nota-dinas.approve',
            'spt.view',
            'spt.create',
            'spt.edit',
            'spt.delete',
            'spt.approve',
            'sppd.view',
            'sppd.create',
            'sppd.edit',
            'sppd.delete',
            'sppd.approve',
            'receipts.view',
            'receipts.create',
            'receipts.edit',
            'receipts.delete',
            'receipts.approve',
            'trip-reports.view',
            'trip-reports.create',
            'trip-reports.edit',
            'trip-reports.delete',
            'trip-reports.approve',
            // Rekap
            'rekap.view',
            'rekap.export',
        ]);

        // 4. Bendahara Pengeluaran Pembantu - mengelola semua dokumen dengan scope bidang, dapat mengakses fitur rekapitulasi sesuai bidang dan master data
        $bendaharaPengeluaranPembantu = Role::firstOrCreate(['name' => 'bendahara-pengeluaran-pembantu']);
        $bendaharaPengeluaranPembantu->syncPermissions([
            // Menu Access
            'menu.dashboard',
            'menu.documents',
            'menu.master-data',
            'menu.rekap',
            // Master Data
            'master-data.view',
            'master-data.create',
            'master-data.edit',
            'users.view',
            'users.create',
            'users.edit',
            // Documents
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
            'trip-reports.view',
            'trip-reports.create',
            'trip-reports.edit',
            'trip-reports.delete',
            // Rekap
            'rekap.view',
            'rekap.export',
        ]);

        // 5. Sekretariat - dapat mengakses semua master data tanpa unit scope
        $sekretariat = Role::firstOrCreate(['name' => 'sekretariat']);
        $sekretariat->syncPermissions([
            // Menu Access
            'menu.dashboard',
            'menu.master-data',
            'menu.rekap',
            // Master Data - Full Access (no unit scope)
            'master-data.view',
            'master-data.create',
            'master-data.edit',
            'master-data.delete',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            // Rekap
            'rekap.view',
            'rekap.export',
        ]);
    }
}
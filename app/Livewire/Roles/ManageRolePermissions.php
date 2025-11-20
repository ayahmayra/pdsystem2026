<?php

namespace App\Livewire\Roles;

use App\Helpers\PermissionHelper;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Kelola Permissions Role'])]

class ManageRolePermissions extends Component
{
    public Role $role;
    public $selectedPermissions = [];
    public $availablePermissions = [];
    public $rolePermissions = [];

    public function mount(Role $role)
    {
        // Check if user has permission to manage permissions (only super-admin)
        if (!PermissionHelper::canManagePermissions()) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
        
        $this->role = $role;
        $this->loadRoleData();
    }

    public function loadRoleData()
    {
        // Load role's current permissions
        $this->rolePermissions = $this->role->permissions->pluck('name')->toArray();
        $this->selectedPermissions = $this->rolePermissions;
        
        // Load all available permissions
        $this->availablePermissions = Permission::orderBy('name')->get();
    }

    public function updatedSelectedPermissions()
    {
        // This will be called when permissions are selected/deselected
    }

    public function savePermissions()
    {
        try {
            // Sync role permissions
            $this->role->syncPermissions($this->selectedPermissions);
            
            // Refresh the role model to get updated permissions
            $this->role->refresh();
            
            session()->flash('message', 'Permissions role berhasil diperbarui.');
            
            // Reload role data
            $this->loadRoleData();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menyimpan permissions: ' . $e->getMessage());
        }
    }

    public function resetToDefault()
    {
        // Reset to default permissions for this role
        $defaultPermissions = $this->getDefaultPermissionsForRole($this->role->name);
        $this->selectedPermissions = $defaultPermissions;
    }

    public function clearAllPermissions()
    {
        $this->selectedPermissions = [];
    }

    public function selectAllPermissions()
    {
        $this->selectedPermissions = $this->availablePermissions->pluck('name')->toArray();
    }

    public function toggleGroupPermissions($groupName)
    {
        $groupPermissions = $this->getPermissionsByGroup($groupName);
        $groupPermissionNames = $groupPermissions->pluck('name')->toArray();
        
        // Check if all group permissions are selected
        $allSelected = collect($groupPermissionNames)->every(function ($permission) {
            return in_array($permission, $this->selectedPermissions);
        });
        
        if ($allSelected) {
            // Remove all group permissions
            $this->selectedPermissions = array_diff($this->selectedPermissions, $groupPermissionNames);
        } else {
            // Add all group permissions
            $this->selectedPermissions = array_unique(array_merge($this->selectedPermissions, $groupPermissionNames));
        }
    }

    public function getDefaultPermissionsForRole($roleName)
    {
        $defaultPermissions = [
            'super-admin' => [
                // Menu Access
                'menu.dashboard', 'menu.documents', 'menu.master-data', 'menu.location-routes', 'menu.reference-rates', 'menu.rekap',
                'menu.configuration', 'menu.organization', 'menu.ranks', 'menu.doc-number-formats', 'menu.number-sequences', 'menu.document-numbers',
                'menu.provinces', 'menu.cities', 'menu.districts', 'menu.org-places', 'menu.transport-modes', 'menu.travel-routes',
                // Master Data
                'master-data.view', 'master-data.create', 'master-data.edit', 'master-data.delete',
                'users.view', 'users.create', 'users.edit', 'users.delete',
                // Documents
                'documents.view', 'documents.create', 'documents.edit', 'documents.delete', 'documents.approve',
                'nota-dinas.view', 'nota-dinas.create', 'nota-dinas.edit', 'nota-dinas.delete', 'nota-dinas.approve',
                'spt.view', 'spt.create', 'spt.edit', 'spt.delete', 'spt.approve',
                'sppd.view', 'sppd.create', 'sppd.edit', 'sppd.delete', 'sppd.approve',
                'receipts.view', 'receipts.create', 'receipts.edit', 'receipts.delete', 'receipts.approve',
                'trip-reports.view', 'trip-reports.create', 'trip-reports.edit', 'trip-reports.delete', 'trip-reports.approve',
                // Rekap
                'rekap.view', 'rekap.export',
                // Reference Rates
                'reference-rates.view', 'reference-rates.create', 'reference-rates.edit', 'reference-rates.delete',
                // Locations
                'locations.view', 'locations.create', 'locations.edit', 'locations.delete',
            ],
            'admin' => [
                // Menu Access
                'menu.dashboard', 'menu.master-data', 'menu.reference-rates', 'menu.rekap',
                'menu.configuration', 'menu.organization', 'menu.ranks', 'menu.doc-number-formats', 'menu.number-sequences', 'menu.document-numbers',
                'menu.provinces', 'menu.cities', 'menu.districts', 'menu.org-places', 'menu.transport-modes', 'menu.travel-routes',
                // Master Data
                'master-data.view', 'master-data.create', 'master-data.edit', 'master-data.delete',
                'users.view', 'users.create', 'users.edit', 'users.delete',
                // Rekap
                'rekap.view', 'rekap.export',
                // Reference Rates
                'reference-rates.view', 'reference-rates.create', 'reference-rates.edit', 'reference-rates.delete',
                // Locations
                'locations.view', 'locations.create', 'locations.edit', 'locations.delete',
            ],
            'bendahara-pengeluaran' => [
                // Menu Access
                'menu.dashboard', 'menu.documents', 'menu.master-data', 'menu.rekap',
                // Master Data
                'master-data.view', 'master-data.create', 'master-data.edit',
                'users.view', 'users.create', 'users.edit',
                // Documents
                'documents.view', 'documents.create', 'documents.edit', 'documents.delete', 'documents.approve',
                'nota-dinas.view', 'nota-dinas.create', 'nota-dinas.edit', 'nota-dinas.delete', 'nota-dinas.approve',
                'spt.view', 'spt.create', 'spt.edit', 'spt.delete', 'spt.approve',
                'sppd.view', 'sppd.create', 'sppd.edit', 'sppd.delete', 'sppd.approve',
                'receipts.view', 'receipts.create', 'receipts.edit', 'receipts.delete', 'receipts.approve',
                'trip-reports.view', 'trip-reports.create', 'trip-reports.edit', 'trip-reports.delete', 'trip-reports.approve',
                // Rekap
                'rekap.view', 'rekap.export',
            ],
            'bendahara-pengeluaran-pembantu' => [
                // Menu Access
                'menu.dashboard', 'menu.documents', 'menu.master-data', 'menu.rekap',
                // Master Data
                'master-data.view', 'master-data.create', 'master-data.edit',
                'users.view', 'users.create', 'users.edit',
                // Documents
                'documents.view', 'documents.create', 'documents.edit', 'documents.delete', 'documents.approve',
                'nota-dinas.view', 'nota-dinas.create', 'nota-dinas.edit', 'nota-dinas.delete', 'nota-dinas.approve',
                'spt.view', 'spt.create', 'spt.edit', 'spt.delete', 'spt.approve',
                'sppd.view', 'sppd.create', 'sppd.edit', 'sppd.delete', 'sppd.approve',
                'receipts.view', 'receipts.create', 'receipts.edit', 'receipts.delete', 'receipts.approve',
                'trip-reports.view', 'trip-reports.create', 'trip-reports.edit', 'trip-reports.delete', 'trip-reports.approve',
                // Rekap
                'rekap.view', 'rekap.export',
            ],
            'sekretariat' => [
                // Menu Access
                'menu.dashboard', 'menu.rekap',
                // Rekap
                'rekap.view', 'rekap.export',
            ],
        ];

        return $defaultPermissions[$roleName] ?? [];
    }

    public function getPermissionsByGroup($groupName)
    {
        return $this->availablePermissions->filter(function ($permission) use ($groupName) {
            return str_starts_with($permission->name, $groupName . '.');
        });
    }

    public function getPermissionGroups()
    {
        $groups = [];
        foreach ($this->availablePermissions as $permission) {
            $parts = explode('.', $permission->name);
            if (count($parts) >= 2) {
                $groupName = $parts[0];
                if (!isset($groups[$groupName])) {
                    $groups[$groupName] = [];
                }
                $groups[$groupName][] = $permission;
            }
        }
        
        // Urutkan groups sesuai dengan struktur menu yang logis
        $orderedGroups = [];
        $groupOrder = [
            // 1. Dokumen (Nota Dinas, SPT, SPPD, Kwitansi, Laporan Perjalanan, Dokumen Pendukung)
            'nota-dinas',
            'spt', 
            'sppd',
            'receipts',
            'trip-reports',
            'supporting-documents',
            'documents',
            
            // 2. Master Data
            'master-data',
            'users',
            
            // 3. Referensi Lokasi & Rute
            'locations',
            'provinces',
            'cities', 
            'districts',
            'org-places',
            'transport-modes',
            'travel-routes',
            
            // 4. Referensi Tarif
            'reference-rates',
            'perdiem-rates',
            'lodging-caps',
            'representation-rates',
            'intra-province-transport-refs',
            'intra-district-transport-refs',
            'official-vehicle-transport-refs',
            'at-cost-components',
            'airfare-refs',
            'district-perdiem-rates',
            
            // 5. Rekap
            'rekap',
            
            // 6. Konfigurasi Sistem (Akses Menu dan Kontrol System)
            'menu',
            'system',
        ];
        
        // Tambahkan groups yang ada dalam urutan yang ditentukan
        foreach ($groupOrder as $groupName) {
            if (isset($groups[$groupName])) {
                $orderedGroups[$groupName] = $groups[$groupName];
                unset($groups[$groupName]);
            }
        }
        
        // Tambahkan groups yang tidak ada dalam urutan (fallback)
        foreach ($groups as $groupName => $permissions) {
            $orderedGroups[$groupName] = $permissions;
        }
        
        return $orderedGroups;
    }

    public function getPermissionDisplayName($permissionName)
    {
        $displayNames = [
            // Menu Access
            'menu.dashboard' => 'Akses Dashboard',
            'menu.documents' => 'Akses Menu Dokumen',
            'menu.master-data' => 'Akses Menu Master Data',
            'menu.location-routes' => 'Akses Menu Lokasi & Rute',
            'menu.reference-rates' => 'Akses Menu Referensi Tarif',
            'menu.rekap' => 'Akses Menu Rekap',
            'menu.configuration' => 'Akses Menu Konfigurasi',
            'menu.organization' => 'Akses Menu Organisasi',
            'menu.ranks' => 'Akses Menu Data Pangkat',
            'menu.doc-number-formats' => 'Akses Menu Format Penomoran Dokumen',
            'menu.number-sequences' => 'Akses Menu Number Sequence',
            'menu.document-numbers' => 'Akses Menu Riwayat Nomor Dokumen',
            // Location & Routes submenu
            'menu.provinces' => 'Akses Menu Data Provinsi',
            'menu.cities' => 'Akses Menu Data Kota/Kabupaten',
            'menu.districts' => 'Akses Menu Data Kecamatan',
            'menu.org-places' => 'Akses Menu Data Kedudukan',
            'menu.transport-modes' => 'Akses Menu Data Moda Transportasi',
            'menu.travel-routes' => 'Akses Menu Data Rute Perjalanan',
            // Master Data
            'master-data.view' => 'Lihat Master Data',
            'master-data.create' => 'Buat Master Data',
            'master-data.edit' => 'Edit Master Data',
            'master-data.delete' => 'Hapus Master Data',
            'users.view' => 'Lihat User',
            'users.create' => 'Buat User',
            'users.edit' => 'Edit User',
            'users.delete' => 'Hapus User',
            // Documents
            'documents.view' => 'Lihat Dokumen',
            'documents.create' => 'Buat Dokumen',
            'documents.edit' => 'Edit Dokumen',
            'documents.delete' => 'Hapus Dokumen',
            'documents.approve' => 'Setujui Dokumen',
            'nota-dinas.view' => 'Lihat Nota Dinas',
            'nota-dinas.create' => 'Buat Nota Dinas',
            'nota-dinas.edit' => 'Edit Nota Dinas',
            'nota-dinas.delete' => 'Hapus Nota Dinas',
            'nota-dinas.approve' => 'Setujui Nota Dinas',
            'spt.view' => 'Lihat SPT',
            'spt.create' => 'Buat SPT',
            'spt.edit' => 'Edit SPT',
            'spt.delete' => 'Hapus SPT',
            'spt.approve' => 'Setujui SPT',
            'sppd.view' => 'Lihat SPPD',
            'sppd.create' => 'Buat SPPD',
            'sppd.edit' => 'Edit SPPD',
            'sppd.delete' => 'Hapus SPPD',
            'sppd.approve' => 'Setujui SPPD',
            'receipts.view' => 'Lihat Kwitansi',
            'receipts.create' => 'Buat Kwitansi',
            'receipts.edit' => 'Edit Kwitansi',
            'receipts.delete' => 'Hapus Kwitansi',
            'receipts.approve' => 'Setujui Kwitansi',
            'trip-reports.view' => 'Lihat Laporan Perjalanan',
            'trip-reports.create' => 'Buat Laporan Perjalanan',
            'trip-reports.edit' => 'Edit Laporan Perjalanan',
            'trip-reports.delete' => 'Hapus Laporan Perjalanan',
            'trip-reports.approve' => 'Setujui Laporan Perjalanan',
            // Rekap
            'rekap.view' => 'Lihat Rekap',
            'rekap.export' => 'Export Rekap',
            // Reference Rates
            'reference-rates.view' => 'Lihat Tarif Referensi',
            'reference-rates.create' => 'Buat Tarif Referensi',
            'reference-rates.edit' => 'Edit Tarif Referensi',
            'reference-rates.delete' => 'Hapus Tarif Referensi',
            // Locations
            'locations.view' => 'Lihat Lokasi',
            'locations.create' => 'Buat Lokasi',
            'locations.edit' => 'Edit Lokasi',
            'locations.delete' => 'Hapus Lokasi',
        ];

        return $displayNames[$permissionName] ?? ucfirst(str_replace(['-', '.'], [' ', ' - '], $permissionName));
    }

    public function getGroupDisplayName($groupName)
    {
        $groupDisplayNames = [
            // 1. Dokumen
            'nota-dinas' => '📄 Nota Dinas',
            'spt' => '📋 Surat Perintah Tugas (SPT)',
            'sppd' => '📑 Surat Perjalanan Perjalanan Dinas (SPPD)',
            'receipts' => '🧾 Kwitansi',
            'trip-reports' => '📊 Laporan Perjalanan Dinas',
            'supporting-documents' => '📎 Dokumen Pendukung',
            'documents' => '📁 Dokumen Umum',
            
            // 2. Master Data
            'master-data' => '🗂️ Master Data',
            'users' => '👥 Manajemen User',
            
            // 3. Referensi Lokasi & Rute
            'locations' => '📍 Lokasi Umum',
            'provinces' => '🏛️ Data Provinsi',
            'cities' => '🏙️ Data Kota/Kabupaten',
            'districts' => '🏘️ Data Kecamatan',
            'org-places' => '🏢 Data Kedudukan',
            'transport-modes' => '🚌 Data Moda Transportasi',
            'travel-routes' => '🛣️ Data Rute Perjalanan',
            
            // 4. Referensi Tarif
            'reference-rates' => '💰 Referensi Tarif Umum',
            'perdiem-rates' => '💵 Data Tarif Uang Harian',
            'lodging-caps' => '🏨 Data Batas Tarif Penginapan',
            'representation-rates' => '🍽️ Data Tarif Representasi',
            'intra-province-transport-refs' => '🚗 Data Referensi Transportasi Dalam Provinsi',
            'intra-district-transport-refs' => '🚙 Data Referensi Transportasi Dalam Kecamatan',
            'official-vehicle-transport-refs' => '🚐 Data Referensi Transportasi Kendaraan Dinas',
            'at-cost-components' => '⚙️ Data Komponen At-Cost',
            'airfare-refs' => '✈️ Data Referensi Tiket Pesawat',
            'district-perdiem-rates' => '💸 Data Tarif Uang Harian Kecamatan',
            
            // 5. Rekap
            'rekap' => '📈 Rekap & Laporan',
            
            // 6. Konfigurasi Sistem
            'menu' => '🔧 Akses Menu',
            'system' => '⚙️ Kontrol Sistem',
        ];

        return $groupDisplayNames[$groupName] ?? ucfirst(str_replace('-', ' ', $groupName));
    }

    public function render()
    {
        return view('livewire.roles.manage-role-permissions');
    }
}

<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Helpers\PermissionHelper;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Kelola Permissions User'])]

class ManagePermissions extends Component
{
    public User $user;
    public $selectedPermissions = [];
    public $availablePermissions = [];
    public $userPermissions = [];
    public $userRoles = [];

    public function mount(User $user)
    {
        // Check if user has permission to manage permissions
        if (!PermissionHelper::canManagePermissions()) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
        
        $this->user = $user;
        $this->loadUserData();
    }

    public function loadUserData()
    {
        // Refresh user model to get latest data
        $this->user->refresh();
        
        // Load user's current permissions (only direct permissions, not from roles)
        $this->userPermissions = $this->user->getDirectPermissions()->pluck('name')->toArray();
        $this->selectedPermissions = $this->userPermissions;
        
        // Load user's roles
        $this->userRoles = $this->user->roles->pluck('name')->toArray();
        
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
            // Clear all direct permissions first
            $this->user->revokePermissionTo($this->user->getDirectPermissions());
            
            // Assign new permissions
            if (!empty($this->selectedPermissions)) {
                $this->user->givePermissionTo($this->selectedPermissions);
            }
            
            // Refresh the user model to get updated permissions
            $this->user->refresh();
            
            session()->flash('message', 'Permissions berhasil diperbarui.');
            
            // Reload user data
            $this->loadUserData();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menyimpan permissions: ' . $e->getMessage());
        }
    }

    public function resetToRolePermissions()
    {
        // Reset to permissions from user's roles only
        $rolePermissions = [];
        foreach ($this->user->roles as $role) {
            $rolePermissions = array_merge($rolePermissions, $role->permissions->pluck('name')->toArray());
        }
        
        $this->selectedPermissions = array_unique($rolePermissions);
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
        $groupPermissions = $this->availablePermissions
            ->filter(function ($permission) use ($groupName) {
                $parts = explode('.', $permission->name);
                return ($parts[0] ?? 'other') === $groupName;
            })
            ->pluck('name')
            ->toArray();

        $allSelected = collect($groupPermissions)->every(function ($permission) {
            return in_array($permission, $this->selectedPermissions);
        });

        if ($allSelected) {
            // Remove all permissions from this group
            $this->selectedPermissions = array_diff($this->selectedPermissions, $groupPermissions);
        } else {
            // Add all permissions from this group
            $this->selectedPermissions = array_unique(array_merge($this->selectedPermissions, $groupPermissions));
        }
    }

    public function getPermissionDisplayName($permissionName)
    {
        $names = [
            'master-data.view' => 'Lihat Master Data',
            'master-data.create' => 'Buat Master Data',
            'master-data.edit' => 'Edit Master Data',
            'master-data.delete' => 'Hapus Master Data',
            'users.view' => 'Lihat User',
            'users.create' => 'Buat User',
            'users.edit' => 'Edit User',
            'users.delete' => 'Hapus User',
            'documents.view' => 'Lihat Dokumen',
            'documents.create' => 'Buat Dokumen',
            'documents.edit' => 'Edit Dokumen',
            'documents.delete' => 'Hapus Dokumen',
            'documents.approve' => 'Approve Dokumen',
            'nota-dinas.view' => 'Lihat Nota Dinas',
            'nota-dinas.create' => 'Buat Nota Dinas',
            'nota-dinas.edit' => 'Edit Nota Dinas',
            'nota-dinas.delete' => 'Hapus Nota Dinas',
            'nota-dinas.approve' => 'Approve Nota Dinas',
            'spt.view' => 'Lihat SPT',
            'spt.create' => 'Buat SPT',
            'spt.edit' => 'Edit SPT',
            'spt.delete' => 'Hapus SPT',
            'spt.approve' => 'Approve SPT',
            'sppd.view' => 'Lihat SPPD',
            'sppd.create' => 'Buat SPPD',
            'sppd.edit' => 'Edit SPPD',
            'sppd.delete' => 'Hapus SPPD',
            'sppd.approve' => 'Approve SPPD',
            'receipts.view' => 'Lihat Kwitansi',
            'receipts.create' => 'Buat Kwitansi',
            'receipts.edit' => 'Edit Kwitansi',
            'receipts.delete' => 'Hapus Kwitansi',
            'receipts.approve' => 'Approve Kwitansi',
            'trip-reports.view' => 'Lihat Laporan Perjalanan',
            'trip-reports.create' => 'Buat Laporan Perjalanan',
            'trip-reports.edit' => 'Edit Laporan Perjalanan',
            'trip-reports.delete' => 'Hapus Laporan Perjalanan',
            'trip-reports.approve' => 'Approve Laporan Perjalanan',
            'rekap.view' => 'Lihat Rekapitulasi',
            'rekap.export' => 'Export Rekapitulasi',
            'reference-rates.view' => 'Lihat Referensi Tarif',
            'reference-rates.create' => 'Buat Referensi Tarif',
            'reference-rates.edit' => 'Edit Referensi Tarif',
            'reference-rates.delete' => 'Hapus Referensi Tarif',
            'locations.view' => 'Lihat Lokasi & Rute',
            'locations.create' => 'Buat Lokasi & Rute',
            'locations.edit' => 'Edit Lokasi & Rute',
            'locations.delete' => 'Hapus Lokasi & Rute',
            'all-access' => 'Akses Penuh',
        ];
        
        return $names[$permissionName] ?? ucfirst(str_replace('-', ' ', $permissionName));
    }

    public function getPermissionGroups()
    {
        $groups = [];
        foreach ($this->availablePermissions as $permission) {
            $parts = explode('.', $permission->name);
            $group = $parts[0] ?? 'other';
            
            if (!isset($groups[$group])) {
                $groups[$group] = [
                    'name' => $this->getGroupDisplayName($group),
                    'permissions' => []
                ];
            }
            
            $groups[$group]['permissions'][] = $permission;
        }
        
        return $groups;
    }

    private function getGroupDisplayName($group)
    {
        $names = [
            'master-data' => 'Master Data',
            'users' => 'User Management',
            'documents' => 'Dokumen',
            'nota-dinas' => 'Nota Dinas',
            'spt' => 'SPT',
            'sppd' => 'SPPD',
            'receipts' => 'Kwitansi',
            'trip-reports' => 'Laporan Perjalanan',
            'rekap' => 'Rekapitulasi',
            'reference-rates' => 'Referensi Tarif',
            'locations' => 'Lokasi & Rute',
            'all-access' => 'Akses Penuh',
        ];
        
        return $names[$group] ?? ucfirst(str_replace('-', ' ', $group));
    }

    public function render()
    {
        $permissionGroups = $this->getPermissionGroups();
        
        return view('livewire.users.manage-permissions', compact('permissionGroups'));
    }
}

<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Helpers\PermissionHelper;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Kelola Role User'])]

class ManageRoles extends Component
{
    public User $user;
    public $selectedRoles = [];
    public $availableRoles = [];
    public $userRoles = [];

    public function mount(User $user)
    {
        // Check if user has permission to manage user roles
        if (!PermissionHelper::canManageUserRoles()) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
        
        $this->user = $user;
        $this->loadUserData();
    }

    public function loadUserData()
    {
        // Refresh user model to get latest data
        $this->user->refresh();
        
        // Load user's current roles
        $this->userRoles = $this->user->roles->pluck('name')->toArray();
        $this->selectedRoles = $this->userRoles;
        
        // Load all available roles (excluding super-admin)
        $this->availableRoles = Role::where('name', '!=', 'super-admin')->orderBy('name')->get();
    }

    public function updatedSelectedRoles()
    {
        // This will be called when roles are selected/deselected
    }

    public function saveRoles()
    {
        try {
            // Sync roles (this will replace all existing roles)
            $this->user->syncRoles($this->selectedRoles);
            
            // Refresh the user model to get updated roles
            $this->user->refresh();
            
            session()->flash('message', 'Role user berhasil diperbarui.');
            
            // Reload user data
            $this->loadUserData();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menyimpan role: ' . $e->getMessage());
        }
    }

    public function clearAllRoles()
    {
        $this->selectedRoles = [];
    }

    public function selectAllRoles()
    {
        $this->selectedRoles = $this->availableRoles->pluck('name')->toArray();
    }

    public function render()
    {
        return view('livewire.users.manage-roles');
    }
}

<?php

namespace App\Livewire\Roles;

use App\Helpers\PermissionHelper;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Daftar Role'])]

class Index extends Component
{
    public $search = '';

    public function mount()
    {
        // Check if user has permission to manage permissions (only super-admin)
        if (!PermissionHelper::canManagePermissions()) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function render()
    {
        $roles = Role::withCount(['users', 'permissions'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->get();

        return view('livewire.roles.index', compact('roles'));
    }
}

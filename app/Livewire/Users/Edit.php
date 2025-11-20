<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Unit;
use App\Models\Position;
use App\Models\Rank;
use App\Models\TravelGrade;
use App\Helpers\PermissionHelper;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Edit Pegawai'])]

class Edit extends Component
{
    public User $user;

    // Form properties
    public $name = '';
    public $email = '';
    public $nip = '';
    public $nik = '';
    public $gelar_depan = '';
    public $gelar_belakang = '';
    public $phone = '';
    public $whatsapp = '';
    public $address = '';
    public $unit_id = '';
    public $position_id = '';
    public $position_desc = '';
    public $rank_id = '';
    public $npwp = '';
    public $bank_name = '';
    public $bank_account_no = '';
    public $bank_account_name = '';
    public $birth_date = '';
    public $gender = '';
    public $is_signer = false;
    public $is_non_staff = false;
    public $travel_grade_id = '';
    public $roles = [];

    // Mutators to handle empty strings for foreign key fields
    public function setUnitIdProperty($value)
    {
        $this->unit_id = $value === '' ? null : $value;
    }

    public function setPositionIdProperty($value)
    {
        $this->position_id = $value === '' ? null : $value;
    }

    public function setRankIdProperty($value)
    {
        $this->rank_id = $value === '' ? null : $value;
    }

    public function setTravelGradeIdProperty($value)
    {
        $this->travel_grade_id = $value === '' ? null : $value;
    }

    public function setBirthDateProperty($value)
    {
        $this->birth_date = ($value === '' || $value === null) ? null : $value;
    }

    public function mount(User $user)
    {
        // Check if user has permission to edit users
        if (!PermissionHelper::can('users.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit user.');
        }

        // Check if user is superadmin and current user is not superadmin
        if ($user->isSuperAdmin() && !\Illuminate\Support\Facades\Auth::user()->isSuperAdmin()) {
            abort(403, 'User dengan role superadmin hanya dapat diubah oleh superadmin lainnya.');
        }
        
        // Check if bendahara pengeluaran pembantu can edit this user
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId && $user->unit_id != $userUnitId) {
                abort(403, 'Anda hanya dapat mengedit pegawai dalam unit yang sama.');
            }
        }
        
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->nip = $user->nip;
        $this->nik = $user->nik;
        $this->gelar_depan = $user->gelar_depan;
        $this->gelar_belakang = $user->gelar_belakang;
        $this->phone = $user->phone;
        $this->whatsapp = $user->whatsapp;
        $this->address = $user->address;
        $this->unit_id = $user->unit_id;
        $this->position_id = $user->position_id;
        $this->position_desc = $user->position_desc;
        $this->rank_id = $user->rank_id;

        $this->npwp = $user->npwp;
        $this->bank_name = $user->bank_name;
        $this->bank_account_no = $user->bank_account_no;
        $this->bank_account_name = $user->bank_account_name;
        $this->birth_date = $user->birth_date?->format('Y-m-d');
        $this->gender = $user->gender;
        $this->is_signer = $user->is_signer;
        $this->is_non_staff = $user->is_non_staff;
        
        // Set travel grade
        $this->travel_grade_id = $user->travel_grade_id;
        
        // Set roles (if user can manage user roles)
        if (PermissionHelper::canManageUserRoles()) {
            $this->roles = $user->roles->pluck('name')->toArray();
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'nip' => 'nullable|string|max:20|unique:users,nip,' . $this->user->id,
            'nik' => 'nullable|string|max:20|unique:users,nik,' . $this->user->id,
            'gelar_depan' => 'nullable|string|max:255',
            'gelar_belakang' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'unit_id' => 'nullable|exists:units,id',
            'position_id' => 'nullable|exists:positions,id',
            'position_desc' => 'nullable|string|max:255',
            'rank_id' => 'nullable|exists:ranks,id',

            'npwp' => 'nullable|string|max:25',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:L,P',
            'is_signer' => 'boolean',
            'is_non_staff' => 'boolean',
            'travel_grade_id' => 'nullable|exists:travel_grades,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ];
    }

    public function update()
    {
        $validated = $this->validate();

        // Check if user is superadmin and current user is not superadmin
        if ($this->user->isSuperAdmin() && !\Illuminate\Support\Facades\Auth::user()->isSuperAdmin()) {
            session()->flash('error', 'User dengan role superadmin hanya dapat diubah oleh superadmin lainnya.');
            return;
        }
        
        // Additional validation for bendahara pengeluaran pembantu
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId && $validated['unit_id'] != $userUnitId) {
                session()->flash('error', 'Anda hanya dapat mengedit pegawai dalam unit yang sama.');
                return;
            }
        }
        
        // Convert empty strings to null for foreign key fields and date fields
        $nullableFields = ['unit_id', 'position_id', 'rank_id', 'travel_grade_id', 'birth_date'];
        foreach ($nullableFields as $key) {
            if (isset($validated[$key]) && ($validated[$key] === '' || $validated[$key] === null)) {
                $validated[$key] = null;
            }
        }
        
        $this->user->update($validated);
        
        // Update roles (only if user can manage user roles)
        if (PermissionHelper::canManageUserRoles()) {
            $this->user->syncRoles($validated['roles'] ?? []);
        }
        
        session()->flash('message', 'Data pegawai berhasil diperbarui.');
        
        return redirect()->route('users.index');
    }

    public function render()
    {
        // Filter units based on user role
        $unitsQuery = Unit::orderBy('name');
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId) {
                $unitsQuery->where('id', $userUnitId);
            }
        }
        $units = $unitsQuery->get();
        
        $positions = Position::with('echelon')
            ->leftJoin('echelons', 'positions.echelon_id', '=', 'echelons.id')
            ->orderByRaw('CASE WHEN echelons.code IS NULL THEN 2 ELSE 0 END') // Non eselon positions last
            ->orderBy('echelons.code', 'asc') // Eselon tertinggi (I.a) first
            ->orderBy('positions.name')
            ->select('positions.*')
            ->get();
        $ranks = Rank::orderBy('code', 'desc')->get(); // Pangkat tertinggi (IV/e) first
        $travelGrades = TravelGrade::orderBy('name')->get();
        $availableRoles = Role::where('name', '!=', 'super-admin')->orderBy('name')->get();
        $canManageRoles = PermissionHelper::canManageUserRoles();

        return view('livewire.users.edit', compact('units', 'positions', 'ranks', 'travelGrades', 'availableRoles', 'canManageRoles'));
    }
}

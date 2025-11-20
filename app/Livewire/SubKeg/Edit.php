<?php

namespace App\Livewire\SubKeg;

use Livewire\Component;
use App\Models\SubKeg;
use App\Models\Unit;
use App\Models\User;
use App\Helpers\PermissionHelper;

class Edit extends Component
{
    public SubKeg $subKeg;
    public $kode_subkeg = '';
    public $nama_subkeg = '';
    public $id_unit = '';
    public $pptk_user_id = '';

    protected $rules = [
        'kode_subkeg' => 'required|string|max:255',
        'nama_subkeg' => 'required|string|max:255',
        'id_unit' => 'nullable|exists:units,id',
        'pptk_user_id' => 'nullable|exists:users,id',
    ];

    protected $messages = [
        'kode_subkeg.required' => 'Kode Sub Kegiatan harus diisi.',
        'nama_subkeg.required' => 'Nama Sub Kegiatan harus diisi.',
        'id_unit.exists' => 'Unit yang dipilih tidak valid.',
        'pptk_user_id.exists' => 'PPTK yang dipilih tidak valid.',
    ];

    public function mount(SubKeg $subKeg)
    {
        // Check if bendahara pengeluaran pembantu can edit this sub kegiatan
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId && $subKeg->id_unit != $userUnitId) {
                abort(403, 'Anda hanya dapat mengedit sub kegiatan dalam unit yang sama.');
            }
        }
        
        $this->subKeg = $subKeg;
        $this->kode_subkeg = $subKeg->kode_subkeg;
        $this->nama_subkeg = $subKeg->nama_subkeg;
        $this->id_unit = $subKeg->id_unit;
        $this->pptk_user_id = $subKeg->pptk_user_id;
    }

    public function update()
    {
        $this->rules['kode_subkeg'] = 'required|string|max:255|unique:sub_keg,kode_subkeg,' . $this->subKeg->id;
        
        $this->validate();
        
        // Additional validation for bendahara pengeluaran pembantu
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId && $this->id_unit != $userUnitId) {
                session()->flash('error', 'Anda hanya dapat mengedit sub kegiatan dalam unit yang sama.');
                return;
            }
        }

        $data = [
            'kode_subkeg' => $this->kode_subkeg,
            'nama_subkeg' => $this->nama_subkeg,
            'id_unit' => $this->id_unit ?: null,
            'pptk_user_id' => $this->pptk_user_id ?: null,
        ];

        $this->subKeg->update($data);

        session()->flash('success', 'Sub Kegiatan berhasil diperbarui.');
        
        return redirect()->route('sub-keg.index');
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
        
        // Filter users based on user role
        $usersQuery = User::orderBy('name');
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId) {
                $usersQuery->where('unit_id', $userUnitId);
            }
        }
        $users = $usersQuery->get();
        
        return view('livewire.sub-keg.edit', [
            'units' => $units,
            'users' => $users,
        ]);
    }
}

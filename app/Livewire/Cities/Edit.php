<?php

namespace App\Livewire\Cities;

use App\Models\City;
use App\Models\Province;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public City $city;
    public $kemendagri_code = '';
    public $province_id = '';
    public $name = '';
    public $type = '';

    protected $rules = [
        'kemendagri_code' => 'required|string|max:10',
        'province_id' => 'required|exists:provinces,id',
        'name' => 'required|string|max:120',
        'type' => 'required|in:KAB,KOTA',
    ];

    protected $messages = [
        'kemendagri_code.required' => 'Kode Kemendagri wajib diisi',
        'province_id.required' => 'Provinsi wajib dipilih',
        'province_id.exists' => 'Provinsi yang dipilih tidak valid',
        'name.required' => 'Nama kota/kabupaten wajib diisi',
        'type.required' => 'Tipe wajib dipilih',
        'type.in' => 'Tipe harus KAB atau KOTA',
    ];

    public function mount(City $city)
    {
        // Check if user has permission to edit locations
        if (!PermissionHelper::can('locations.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit kota/kabupaten.');
        }
        
        $this->city = $city;
        $this->kemendagri_code = $city->kemendagri_code;
        $this->province_id = $city->province_id;
        $this->name = $city->name;
        $this->type = $city->type;
    }

    public function setProvinceIdProperty($value)
    {
        $this->province_id = $value === '' ? null : $value;
    }

    public function update()
    {
        // Check if user has permission to edit locations
        if (!PermissionHelper::can('locations.edit')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengedit kota/kabupaten.');
            return;
        }
        
        $this->validate([
            'kemendagri_code' => 'required|string|max:10|unique:cities,kemendagri_code,' . $this->city->id,
            'province_id' => 'required|exists:provinces,id',
            'name' => 'required|string|max:120',
            'type' => 'required|in:KAB,KOTA',
        ]);

        // Manual conversion for empty string to null
        if ($this->province_id === '') {
            $this->province_id = null;
        }

        $this->city->update([
            'kemendagri_code' => $this->kemendagri_code,
            'province_id' => $this->province_id,
            'name' => $this->name,
            'type' => $this->type,
        ]);

        session()->flash('message', 'Kota/Kabupaten berhasil diperbarui');
        return redirect()->route('cities.index');
    }

    public function render()
    {
        $provinces = Province::orderBy('name')->get();
        
        return view('livewire.cities.edit', [
            'provinces' => $provinces,
        ]);
    }
}

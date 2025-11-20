<?php

namespace App\Livewire\Cities;

use App\Models\City;
use App\Models\Province;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public $kemendagri_code = '';
    public $province_id = '';
    public $name = '';
    public $type = '';

    protected $rules = [
        'kemendagri_code' => 'required|string|max:10|unique:cities,kemendagri_code',
        'province_id' => 'required|exists:provinces,id',
        'name' => 'required|string|max:120',
        'type' => 'required|in:KAB,KOTA',
    ];

    protected $messages = [
        'kemendagri_code.required' => 'Kode Kemendagri wajib diisi',
        'kemendagri_code.unique' => 'Kode Kemendagri sudah ada',
        'province_id.required' => 'Provinsi wajib dipilih',
        'province_id.exists' => 'Provinsi yang dipilih tidak valid',
        'name.required' => 'Nama kota/kabupaten wajib diisi',
        'type.required' => 'Tipe wajib dipilih',
        'type.in' => 'Tipe harus KAB atau KOTA',
    ];

    public function mount()
    {
        // Check if user has permission to create locations
        if (!PermissionHelper::can('locations.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat kota/kabupaten.');
        }
    }

    public function setProvinceIdProperty($value)
    {
        $this->province_id = $value === '' ? null : $value;
    }

    public function save()
    {
        // Check if user has permission to create locations
        if (!PermissionHelper::can('locations.create')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk membuat kota/kabupaten.');
            return;
        }
        
        $this->validate();

        // Manual conversion for empty string to null
        if ($this->province_id === '') {
            $this->province_id = null;
        }

        City::create([
            'kemendagri_code' => $this->kemendagri_code,
            'province_id' => $this->province_id,
            'name' => $this->name,
            'type' => $this->type,
        ]);

        session()->flash('message', 'Kota/Kabupaten berhasil ditambahkan');
        return redirect()->route('cities.index');
    }

    public function render()
    {
        $provinces = Province::orderBy('name')->get();
        
        return view('livewire.cities.create', [
            'provinces' => $provinces,
        ]);
    }
}

<?php

namespace App\Livewire\Districts;

use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public $kemendagri_code = '';
    public $city_id = '';
    public $name = '';
    public $selected_province_id = '';

    protected $rules = [
        'kemendagri_code' => 'required|string|max:10|unique:districts,kemendagri_code',
        'city_id' => 'required|exists:cities,id',
        'name' => 'required|string|max:120',
    ];

    protected $messages = [
        'kemendagri_code.required' => 'Kode Kemendagri wajib diisi',
        'kemendagri_code.unique' => 'Kode Kemendagri sudah ada',
        'city_id.required' => 'Kota/Kabupaten wajib dipilih',
        'city_id.exists' => 'Kota/Kabupaten yang dipilih tidak valid',
        'name.required' => 'Nama kecamatan wajib diisi',
    ];

    public function setCityIdProperty($value)
    {
        $this->city_id = $value === '' ? null : $value;
    }

    public function setSelectedProvinceIdProperty($value)
    {
        $this->selected_province_id = $value === '' ? null : $value;
        $this->city_id = ''; // Reset city selection when province changes
    }

    public function mount()
    {
        // Check if user has permission to create locations
        if (!PermissionHelper::can('locations.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat kecamatan.');
        }
    }

    public function save()
    {
        // Check if user has permission to create locations
        if (!PermissionHelper::can('locations.create')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk membuat kecamatan.');
            return;
        }
        
        $this->validate();

        // Manual conversion for empty string to null
        if ($this->city_id === '') {
            $this->city_id = null;
        }

        District::create([
            'kemendagri_code' => $this->kemendagri_code,
            'city_id' => $this->city_id,
            'name' => $this->name,
        ]);

        session()->flash('message', 'Kecamatan berhasil ditambahkan');
        return redirect()->route('districts.index');
    }

    public function render()
    {
        $provinces = Province::orderBy('name')->get();
        $cities = collect();
        
        if ($this->selected_province_id) {
            $cities = City::where('province_id', $this->selected_province_id)
                         ->orderBy('name')
                         ->get();
        }
        
        return view('livewire.districts.create', [
            'provinces' => $provinces,
            'cities' => $cities,
        ]);
    }
}

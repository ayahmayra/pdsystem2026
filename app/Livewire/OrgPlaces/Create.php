<?php

namespace App\Livewire\OrgPlaces;

use App\Models\City;
use App\Models\District;
use App\Models\OrgPlace;
use App\Models\Province;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public $name = '';
    public $city_id = '';
    public $district_id = '';
    public $is_org_headquarter = false;
    public $selected_province_id = '';
    public $selected_city_id = '';

    protected $rules = [
        'name' => 'required|string|max:120|unique:org_places,name',
        'city_id' => 'nullable|exists:cities,id',
        'district_id' => 'nullable|exists:districts,id',
        'is_org_headquarter' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Nama kedudukan wajib diisi',
        'name.unique' => 'Nama kedudukan sudah ada',
        'city_id.exists' => 'Kota/Kabupaten yang dipilih tidak valid',
        'district_id.exists' => 'Kecamatan yang dipilih tidak valid',
    ];

    public function setCityIdProperty($value)
    {
        $this->city_id = $value === '' ? null : $value;
        $this->district_id = ''; // Reset district selection when city changes
    }

    public function setDistrictIdProperty($value)
    {
        $this->district_id = $value === '' ? null : $value;
    }

    public function setSelectedProvinceIdProperty($value)
    {
        $this->selected_province_id = $value === '' ? null : $value;
        $this->selected_city_id = ''; // Reset city selection when province changes
        $this->city_id = ''; // Reset city_id when province changes
        $this->district_id = ''; // Reset district selection when province changes
    }

    public function setSelectedCityIdProperty($value)
    {
        $this->selected_city_id = $value === '' ? null : $value;
        $this->city_id = $value === '' ? null : $value;
        $this->district_id = ''; // Reset district selection when city changes
    }

    public function mount()
    {
        // Check if user has permission to create locations
        if (!PermissionHelper::can('locations.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat kedudukan.');
        }
    }

    public function save()
    {
        // Check if user has permission to create locations
        if (!PermissionHelper::can('locations.create')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk membuat kedudukan.');
            return;
        }
        
        $this->validate();

        // Manual conversion for empty string to null
        if ($this->city_id === '') {
            $this->city_id = null;
        }
        if ($this->district_id === '') {
            $this->district_id = null;
        }

        OrgPlace::create([
            'name' => $this->name,
            'city_id' => $this->city_id,
            'district_id' => $this->district_id,
            'is_org_headquarter' => $this->is_org_headquarter,
        ]);

        session()->flash('message', 'Kedudukan organisasi berhasil ditambahkan');
        return redirect()->route('org-places.index');
    }

    public function render()
    {
        $provinces = Province::orderBy('name')->get();
        $cities = collect();
        $districts = collect();
        
        if ($this->selected_province_id) {
            $cities = City::where('province_id', $this->selected_province_id)
                         ->orderBy('name')
                         ->get();
        }
        
        if ($this->selected_city_id) {
            $districts = District::where('city_id', $this->selected_city_id)
                                ->orderBy('name')
                                ->get();
        }
        
        return view('livewire.org-places.create', [
            'provinces' => $provinces,
            'cities' => $cities,
            'districts' => $districts,
        ]);
    }
}

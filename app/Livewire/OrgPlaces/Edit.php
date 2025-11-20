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
class Edit extends Component
{
    public OrgPlace $orgPlace;
    public $name = '';
    public $city_id = '';
    public $district_id = '';
    public $is_org_headquarter = false;
    public $selected_province_id = '';
    public $selected_city_id = '';

    protected $rules = [
        'name' => 'required|string|max:120',
        'city_id' => 'nullable|exists:cities,id',
        'district_id' => 'nullable|exists:districts,id',
        'is_org_headquarter' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Nama kedudukan wajib diisi',
        'city_id.exists' => 'Kota/Kabupaten yang dipilih tidak valid',
        'district_id.exists' => 'Kecamatan yang dipilih tidak valid',
    ];

    public function mount(OrgPlace $orgPlace)
    {
        // Check if user has permission to edit locations
        if (!PermissionHelper::can('locations.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit kedudukan.');
        }
        
        $this->orgPlace = $orgPlace;
        $this->name = $orgPlace->name;
        $this->city_id = $orgPlace->city_id;
        $this->district_id = $orgPlace->district_id;
        $this->is_org_headquarter = $orgPlace->is_org_headquarter;
        
        // Set selected values for cascading dropdowns
        if ($orgPlace->city) {
            $this->selected_province_id = $orgPlace->city->province_id;
            $this->selected_city_id = $orgPlace->city_id;
        }
    }

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

    public function update()
    {
        // Check if user has permission to edit locations
        if (!PermissionHelper::can('locations.edit')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengedit kedudukan.');
            return;
        }
        
        $this->validate([
            'name' => 'required|string|max:120|unique:org_places,name,' . $this->orgPlace->id,
            'city_id' => 'nullable|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'is_org_headquarter' => 'boolean',
        ]);

        // Manual conversion for empty string to null
        if ($this->city_id === '') {
            $this->city_id = null;
        }
        if ($this->district_id === '') {
            $this->district_id = null;
        }

        $this->orgPlace->update([
            'name' => $this->name,
            'city_id' => $this->city_id,
            'district_id' => $this->district_id,
            'is_org_headquarter' => $this->is_org_headquarter,
        ]);

        session()->flash('message', 'Kedudukan organisasi berhasil diperbarui');
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
        
        return view('livewire.org-places.edit', [
            'provinces' => $provinces,
            'cities' => $cities,
            'districts' => $districts,
        ]);
    }
}

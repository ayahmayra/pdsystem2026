<?php

namespace App\Livewire\DistrictPerdiemRates;

use App\Models\DistrictPerdiemRate;
use App\Models\District;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Rule;

class Edit extends Component
{
    public DistrictPerdiemRate $districtPerdiemRate;

    #[Rule('required|exists:districts,id')]
    public $district_id = '';

    #[Rule('required|exists:travel_grades,id')]
    public $travel_grade_id = '';

    #[Rule('required|numeric|min:0')]
    public $perdiem_rate = '';

    public function mount(DistrictPerdiemRate $districtPerdiemRate)
    {
        // Check if user has permission to edit reference rates
        if (!PermissionHelper::can('reference-rates.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit data.');
        }
        
        $this->districtPerdiemRate = $districtPerdiemRate;
        $this->district_id = $districtPerdiemRate->district_id;
        $this->travel_grade_id = $districtPerdiemRate->travel_grade_id;
        $this->perdiem_rate = $districtPerdiemRate->perdiem_rate;
    }

    public function update()
    {
        // Check if user has permission to edit reference rates
        if (!PermissionHelper::can('reference-rates.edit')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengedit data.');
            return;
        }
        
        $this->validate();

        // Check for duplicate (excluding current record)
        $existing = DistrictPerdiemRate::where('district_id', $this->district_id)
            ->where('travel_grade_id', $this->travel_grade_id)
            ->where('id', '!=', $this->districtPerdiemRate->id)
            ->first();

        if ($existing) {
            $this->addError('district_id', 'Tarif untuk kecamatan dan tingkatan ini sudah ada.');
            return;
        }

        $this->districtPerdiemRate->update([
            'district_id' => $this->district_id,
            'travel_grade_id' => $this->travel_grade_id,
            'perdiem_rate' => $this->perdiem_rate,
        ]);

        session()->flash('message', 'Tarif uang harian kecamatan berhasil diperbarui.');

        return redirect()->route('district-perdiem-rates.index');
    }

    public function render()
    {
        $districts = District::orderBy('name')->get();
        $travelGrades = \App\Models\TravelGrade::orderBy('name')->get();

        return view('livewire.district-perdiem-rates.edit', [
            'districts' => $districts,
            'travelGrades' => $travelGrades,
        ]);
    }
}

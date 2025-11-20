<?php

namespace App\Livewire\IntraDistrictTransportRefs;

use App\Models\IntraDistrictTransportRef;
use App\Models\OrgPlace;
use App\Models\District;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('components.layouts.app')]
class Create extends Component
{
    #[Rule('required|exists:org_places,id')]
    public $origin_place_id = '';

    #[Rule('required|exists:districts,id')]
    public $destination_district_id = '';

    #[Rule('required|numeric|min:0')]
    public $pp_amount = '';

    public function mount()
    {
        // Check if user has permission to create reference rates
        if (!PermissionHelper::can('reference-rates.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat data.');
        }
    }

    public function save()
    {
        // Check if user has permission to create reference rates
        if (!PermissionHelper::can('reference-rates.create')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk membuat data.');
            return;
        }
        
        $this->validate();

        // Cek apakah kombinasi sudah ada
        $existing = IntraDistrictTransportRef::where('origin_place_id', $this->origin_place_id)
            ->where('destination_district_id', $this->destination_district_id)
            ->first();

        if ($existing) {
            $this->addError('destination_district_id', 'Referensi untuk kombinasi tempat kerja dan kecamatan ini sudah ada.');
            return;
        }

        IntraDistrictTransportRef::create([
            'origin_place_id' => $this->origin_place_id,
            'destination_district_id' => $this->destination_district_id,
            'pp_amount' => $this->pp_amount,
        ]);

        session()->flash('message', 'Referensi transportasi dalam kecamatan berhasil ditambahkan');
        return $this->redirect(route('intra-district-transport-refs.index'));
    }

    public function render()
    {
        $orgPlaces = OrgPlace::with('city.province')
            ->whereHas('city.province') // Pastikan hanya org places yang memiliki city dan province
            ->orderBy('name')
            ->get();
        $districts = District::with('city.province')
            ->whereHas('city.province') // Pastikan hanya districts yang memiliki city dan province
            ->orderBy('name')
            ->get();

        return view('livewire.intra-district-transport-refs.create', [
            'orgPlaces' => $orgPlaces,
            'districts' => $districts,
        ]);
    }
}

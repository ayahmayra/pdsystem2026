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
class Edit extends Component
{
    public IntraDistrictTransportRef $transportRef;

    #[Rule('required|exists:org_places,id')]
    public $origin_place_id = '';

    #[Rule('required|exists:districts,id')]
    public $destination_district_id = '';

    #[Rule('required|numeric|min:0')]
    public $pp_amount = '';

    public function mount(IntraDistrictTransportRef $transportRef)
    {
        // Check if user has permission to edit reference rates
        if (!PermissionHelper::can('reference-rates.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit data.');
        }
        
        $this->transportRef = $transportRef;
        $this->origin_place_id = $this->transportRef->origin_place_id;
        $this->destination_district_id = $this->transportRef->destination_district_id;
        $this->pp_amount = $this->transportRef->pp_amount;
    }

    public function save()
    {
        // Check if user has permission to edit reference rates
        if (!PermissionHelper::can('reference-rates.edit')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengedit data.');
            return;
        }
        
        $this->validate();

        // Cek apakah kombinasi sudah ada (kecuali record yang sedang diedit)
        $existing = IntraDistrictTransportRef::where('origin_place_id', $this->origin_place_id)
            ->where('destination_district_id', $this->destination_district_id)
            ->where('id', '!=', $this->transportRef->id)
            ->first();

        if ($existing) {
            $this->addError('destination_district_id', 'Referensi untuk kombinasi tempat kerja dan kecamatan ini sudah ada.');
            return;
        }

        $this->transportRef->update([
            'origin_place_id' => $this->origin_place_id,
            'destination_district_id' => $this->destination_district_id,
            'pp_amount' => $this->pp_amount,
        ]);

        session()->flash('message', 'Referensi transportasi dalam kecamatan berhasil diperbarui');
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

        return view('livewire.intra-district-transport-refs.edit', [
            'orgPlaces' => $orgPlaces,
            'districts' => $districts,
        ]);
    }
}

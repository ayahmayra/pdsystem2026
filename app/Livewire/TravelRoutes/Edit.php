<?php

namespace App\Livewire\TravelRoutes;

use App\Models\TravelRoute;
use App\Models\OrgPlace;
use App\Models\TransportMode;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public TravelRoute $travelRoute;
    public $origin_place_id = '';
    public $destination_place_id = '';
    public $mode_id = '';
    public $is_roundtrip = false;
    public $class = null;

    protected $rules = [
        'origin_place_id' => 'required|exists:org_places,id',
        'destination_place_id' => 'required|exists:org_places,id|different:origin_place_id',
        'mode_id' => 'required|exists:transport_modes,id',
        'is_roundtrip' => 'boolean',
        'class' => 'nullable|in:ECONOMY,BUSINESS',
    ];

    protected $messages = [
        'origin_place_id.required' => 'Tempat asal wajib dipilih',
        'origin_place_id.exists' => 'Tempat asal tidak valid',
        'destination_place_id.required' => 'Tempat tujuan wajib dipilih',
        'destination_place_id.exists' => 'Tempat tujuan tidak valid',
        'destination_place_id.different' => 'Tempat asal dan tujuan tidak boleh sama',
        'mode_id.required' => 'Moda transportasi wajib dipilih',
        'mode_id.exists' => 'Moda transportasi tidak valid',
        'class.in' => 'Kelas tidak valid',
    ];

    public function mount(TravelRoute $travelRoute)
    {
        // Check if user has permission to edit locations
        if (!PermissionHelper::can('locations.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit rute perjalanan.');
        }
        
        $this->travelRoute = $travelRoute;
        $this->origin_place_id = $travelRoute->origin_place_id;
        $this->destination_place_id = $travelRoute->destination_place_id;
        $this->mode_id = $travelRoute->mode_id;
        $this->is_roundtrip = $travelRoute->is_roundtrip;
        $this->class = $travelRoute->class;
    }

    public function setOriginPlaceIdProperty($value)
    {
        $this->origin_place_id = $value === '' ? null : $value;
    }

    public function setDestinationPlaceIdProperty($value)
    {
        $this->destination_place_id = $value === '' ? null : $value;
    }

    public function setModeIdProperty($value)
    {
        $this->mode_id = $value === '' ? null : $value;
        // Reset class jika bukan moda AIR
        if ($value && $value !== '') {
            $mode = TransportMode::find($value);
            if ($mode && $mode->code !== 'UDARA') {
                $this->class = null;
            }
        }
    }

    public function setClassProperty($value)
    {
        $this->class = $value === '' ? null : $value;
    }

    public function update()
    {
        // Check if user has permission to edit locations
        if (!PermissionHelper::can('locations.edit')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengedit rute perjalanan.');
            return;
        }
        
        $this->validate();

        // Ensure class is null if not AIR mode
        $finalClass = $this->class;
        if ($this->mode_id) {
            $mode = TransportMode::find($this->mode_id);
            if ($mode && $mode->code !== 'UDARA') {
                $finalClass = null;
            }
        }

        // Check for duplicate route (excluding current route)
        $existingRoute = TravelRoute::where([
            'origin_place_id' => $this->origin_place_id,
            'destination_place_id' => $this->destination_place_id,
            'mode_id' => $this->mode_id,
            'class' => $finalClass,
        ])->where('id', '!=', $this->travelRoute->id)->first();

        if ($existingRoute) {
            session()->flash('error', 'Rute perjalanan dengan kombinasi yang sama sudah ada');
            return;
        }

        $this->travelRoute->update([
            'origin_place_id' => $this->origin_place_id,
            'destination_place_id' => $this->destination_place_id,
            'mode_id' => $this->mode_id,
            'is_roundtrip' => $this->is_roundtrip,
            'class' => $finalClass,
        ]);

        session()->flash('message', 'Rute perjalanan berhasil diperbarui');
        return redirect()->route('travel-routes.index');
    }

    public function render()
    {
        $orgPlaces = OrgPlace::orderBy('name')->get();
        $transportModes = TransportMode::orderBy('name')->get();
        $airMode = TransportMode::where('code', 'UDARA')->first();

        return view('livewire.travel-routes.edit', [
            'orgPlaces' => $orgPlaces,
            'transportModes' => $transportModes,
            'airMode' => $airMode,
        ]);
    }
}

<?php

namespace App\Livewire\AirfareRefs;

use App\Models\AirfareRef;
use App\Models\City;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public AirfareRef $airfareRef;
    public $origin_city_id = '';
    public $destination_city_id = '';
    public $class = 'ECONOMY';
    public $pp_estimate = '';

    protected $rules = [
        'origin_city_id' => 'required|exists:cities,id',
        'destination_city_id' => 'required|exists:cities,id|different:origin_city_id',
        'class' => 'required|in:ECONOMY,BUSINESS',
        'pp_estimate' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'origin_city_id.required' => 'Kota asal wajib dipilih',
        'origin_city_id.exists' => 'Kota asal yang dipilih tidak valid',
        'destination_city_id.required' => 'Kota tujuan wajib dipilih',
        'destination_city_id.exists' => 'Kota tujuan yang dipilih tidak valid',
        'destination_city_id.different' => 'Kota tujuan harus berbeda dengan kota asal',
        'class.required' => 'Kelas wajib dipilih',
        'class.in' => 'Kelas harus ECONOMY atau BUSINESS',
        'pp_estimate.required' => 'Estimasi per orang wajib diisi',
        'pp_estimate.numeric' => 'Estimasi per orang harus berupa angka',
        'pp_estimate.min' => 'Estimasi per orang minimal 0',
    ];

    public function mount(AirfareRef $airfareRef)
    {
        // Check if user has permission to edit reference rates
        if (!PermissionHelper::can('reference-rates.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit data.');
        }
        
        $this->airfareRef = $airfareRef;
        $this->origin_city_id = $airfareRef->origin_city_id;
        $this->destination_city_id = $airfareRef->destination_city_id;
        $this->class = $airfareRef->class;
        $this->pp_estimate = $airfareRef->pp_estimate;
    }

    public function update()
    {
        // Check if user has permission to edit reference rates
        if (!PermissionHelper::can('reference-rates.edit')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengedit data.');
            return;
        }
        
        $this->validate();

        // Check for duplicate combination (excluding current record)
        $existingRef = AirfareRef::where([
            'origin_city_id' => $this->origin_city_id,
            'destination_city_id' => $this->destination_city_id,
            'class' => $this->class,
        ])->where('id', '!=', $this->airfareRef->id)->first();

        if ($existingRef) {
            session()->flash('error', 'Referensi tiket untuk kombinasi rute dan kelas ini sudah ada');
            return;
        }

        $this->airfareRef->update([
            'origin_city_id' => $this->origin_city_id,
            'destination_city_id' => $this->destination_city_id,
            'class' => $this->class,
            'pp_estimate' => $this->pp_estimate,
        ]);

        session()->flash('message', 'Referensi tiket pesawat berhasil diperbarui');
        return redirect()->route('airfare-refs.index');
    }

    public function render()
    {
        $cities = City::with('province')->orderBy('name')->get();

        return view('livewire.airfare-refs.edit', compact('cities'));
    }
}

<?php

namespace App\Livewire\AirfareRefs;

use App\Models\AirfareRef;
use App\Models\City;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Create extends Component
{
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

        // Check for duplicate combination
        $existingRef = AirfareRef::where([
            'origin_city_id' => $this->origin_city_id,
            'destination_city_id' => $this->destination_city_id,
            'class' => $this->class,
        ])->first();

        if ($existingRef) {
            session()->flash('error', 'Referensi tiket untuk kombinasi rute dan kelas ini sudah ada');
            return;
        }

        AirfareRef::create([
            'origin_city_id' => $this->origin_city_id,
            'destination_city_id' => $this->destination_city_id,
            'class' => $this->class,
            'pp_estimate' => $this->pp_estimate,
        ]);

        session()->flash('message', 'Referensi tiket pesawat berhasil ditambahkan');
        return redirect()->route('airfare-refs.index');
    }

    public function render()
    {
        $cities = City::with('province')->orderBy('name')->get();

        return view('livewire.airfare-refs.create', compact('cities'));
    }
}

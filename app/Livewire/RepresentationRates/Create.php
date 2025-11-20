<?php

namespace App\Livewire\RepresentationRates;

use App\Models\RepresentationRate;
use App\Models\TravelGrade;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public $travel_grade_id = '';
    public $satuan = 'OH';
    public $luar_kota = '';
    public $dalam_kota_gt8h = '';

    protected $rules = [
        'travel_grade_id' => 'required|exists:travel_grades,id',
        'satuan' => 'required|string|max:10',
        'luar_kota' => 'required|numeric|min:0',
        'dalam_kota_gt8h' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'travel_grade_id.required' => 'Tingkatan perjalanan wajib dipilih',
        'travel_grade_id.exists' => 'Tingkatan yang dipilih tidak valid',
        'satuan.required' => 'Satuan wajib diisi',
        'satuan.max' => 'Satuan maksimal 10 karakter',
        'luar_kota.required' => 'Tarif luar kota wajib diisi',
        'luar_kota.numeric' => 'Tarif luar kota harus berupa angka',
        'luar_kota.min' => 'Tarif luar kota minimal 0',
        'dalam_kota_gt8h.required' => 'Tarif dalam kota >8 jam wajib diisi',
        'dalam_kota_gt8h.numeric' => 'Tarif dalam kota >8 jam harus berupa angka',
        'dalam_kota_gt8h.min' => 'Tarif dalam kota >8 jam minimal 0',
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

        // Check for duplicate travel grade
        $existingRate = RepresentationRate::where('travel_grade_id', $this->travel_grade_id)->first();

        if ($existingRate) {
            session()->flash('error', 'Tarif representasi untuk tingkatan ini sudah ada');
            return;
        }

        RepresentationRate::create([
            'travel_grade_id' => $this->travel_grade_id,
            'satuan' => $this->satuan,
            'luar_kota' => $this->luar_kota,
            'dalam_kota_gt8h' => $this->dalam_kota_gt8h,
        ]);

        session()->flash('message', 'Tarif representasi berhasil ditambahkan');
        return redirect()->route('representation-rates.index');
    }

    public function render()
    {
        // Hanya tampilkan tingkatan yang belum memiliki tarif representasi
        $travelGrades = TravelGrade::whereNotIn('id', function($query) {
            $query->select('travel_grade_id')->from('representation_rates');
        })->orderBy('name')->get();

        return view('livewire.representation-rates.create', compact('travelGrades'));
    }
}

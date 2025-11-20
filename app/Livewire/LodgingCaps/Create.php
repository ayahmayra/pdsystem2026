<?php

namespace App\Livewire\LodgingCaps;

use App\Models\LodgingCap;
use App\Models\Province;
use App\Models\TravelGrade;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public $province_id = '';
    public $travel_grade_id = '';
    public $cap_amount = '';

    protected $rules = [
        'province_id' => 'required|exists:provinces,id',
        'travel_grade_id' => 'required|exists:travel_grades,id',
        'cap_amount' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'province_id.required' => 'Provinsi wajib dipilih',
        'province_id.exists' => 'Provinsi yang dipilih tidak valid',
        'travel_grade_id.required' => 'Tingkatan perjalanan wajib dipilih',
        'travel_grade_id.exists' => 'Tingkatan yang dipilih tidak valid',
        'cap_amount.required' => 'Batas tarif wajib diisi',
        'cap_amount.numeric' => 'Batas tarif harus berupa angka',
        'cap_amount.min' => 'Batas tarif minimal 0',
    ];

    public function mount()
    {
        // Check if user has permission to create reference rates
        if (!PermissionHelper::can('reference-rates.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat batas tarif penginapan.');
        }
    }

    public function save()
    {
        // Check if user has permission to create reference rates
        if (!PermissionHelper::can('reference-rates.create')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk membuat batas tarif penginapan.');
            return;
        }
        
        $this->validate();

        // Check for duplicate combination
        $existingCap = LodgingCap::where([
            'province_id' => $this->province_id,
            'travel_grade_id' => $this->travel_grade_id,
        ])->first();

        if ($existingCap) {
            session()->flash('error', 'Batas tarif untuk kombinasi provinsi dan tingkatan ini sudah ada');
            return;
        }

        LodgingCap::create([
            'province_id' => $this->province_id,
            'travel_grade_id' => $this->travel_grade_id,
            'cap_amount' => $this->cap_amount,
        ]);

        session()->flash('message', 'Batas tarif penginapan berhasil ditambahkan');
        return redirect()->route('lodging-caps.index');
    }

    public function render()
    {
        $provinces = Province::orderBy('name')->get();
        $travelGrades = TravelGrade::orderBy('name')->get();

        return view('livewire.lodging-caps.create', compact('provinces', 'travelGrades'));
    }
}

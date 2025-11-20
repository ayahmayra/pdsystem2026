<?php

namespace App\Livewire\LodgingCaps;

use App\Models\LodgingCap;
use App\Models\Province;
use App\Models\TravelGrade;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public LodgingCap $lodgingCap;
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

    public function mount(LodgingCap $lodgingCap)
    {
        // Check if user has permission to edit reference rates
        if (!PermissionHelper::can('reference-rates.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit batas tarif penginapan.');
        }
        
        $this->lodgingCap = $lodgingCap;
        $this->province_id = $lodgingCap->province_id;
        $this->travel_grade_id = $lodgingCap->travel_grade_id;
        $this->cap_amount = $lodgingCap->cap_amount;
    }

    public function update()
    {
        // Check if user has permission to edit reference rates
        if (!PermissionHelper::can('reference-rates.edit')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengedit batas tarif penginapan.');
            return;
        }
        
        $this->validate();

        // Check for duplicate combination (excluding current record)
        $existingCap = LodgingCap::where([
            'province_id' => $this->province_id,
            'travel_grade_id' => $this->travel_grade_id,
        ])->where('id', '!=', $this->lodgingCap->id)->first();

        if ($existingCap) {
            session()->flash('error', 'Batas tarif untuk kombinasi provinsi dan tingkatan ini sudah ada');
            return;
        }

        $this->lodgingCap->update([
            'province_id' => $this->province_id,
            'travel_grade_id' => $this->travel_grade_id,
            'cap_amount' => $this->cap_amount,
        ]);

        session()->flash('message', 'Batas tarif penginapan berhasil diperbarui');
        return redirect()->route('lodging-caps.index');
    }

    public function render()
    {
        $provinces = Province::orderBy('name')->get();
        $travelGrades = TravelGrade::orderBy('name')->get();

        return view('livewire.lodging-caps.edit', compact('provinces', 'travelGrades'));
    }
}

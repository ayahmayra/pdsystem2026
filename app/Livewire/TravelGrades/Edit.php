<?php

namespace App\Livewire\TravelGrades;

use App\Models\TravelGrade;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public TravelGrade $travelGrade;
    public $code = '';
    public $name = '';

    protected $rules = [
        'code' => 'required|string|max:50',
        'name' => 'required|string|max:200',
    ];

    protected $messages = [
        'code.required' => 'Kode tingkatan wajib diisi',
        'code.max' => 'Kode tingkatan maksimal 50 karakter',
        'name.required' => 'Nama tingkatan wajib diisi',
        'name.max' => 'Nama tingkatan maksimal 200 karakter',
    ];

    public function mount(TravelGrade $travelGrade)
    {
        $this->travelGrade = $travelGrade;
        $this->code = $travelGrade->code;
        $this->name = $travelGrade->name;
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|string|max:50|unique:travel_grades,code,' . $this->travelGrade->id,
            'name' => 'required|string|max:200',
        ]);

        $this->travelGrade->update([
            'code' => strtoupper($this->code),
            'name' => $this->name,
        ]);

        session()->flash('message', 'Tingkatan perjalanan berhasil diperbarui');
        return redirect()->route('travel-grades.index');
    }

    public function render()
    {
        return view('livewire.travel-grades.edit');
    }
}

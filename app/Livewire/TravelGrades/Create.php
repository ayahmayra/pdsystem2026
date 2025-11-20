<?php

namespace App\Livewire\TravelGrades;

use App\Models\TravelGrade;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public $code = '';
    public $name = '';

    protected $rules = [
        'code' => 'required|string|max:50|unique:travel_grades,code',
        'name' => 'required|string|max:200',
    ];

    protected $messages = [
        'code.required' => 'Kode tingkatan wajib diisi',
        'code.unique' => 'Kode tingkatan sudah digunakan',
        'code.max' => 'Kode tingkatan maksimal 50 karakter',
        'name.required' => 'Nama tingkatan wajib diisi',
        'name.max' => 'Nama tingkatan maksimal 200 karakter',
    ];

    public function save()
    {
        $this->validate();

        TravelGrade::create([
            'code' => strtoupper($this->code),
            'name' => $this->name,
        ]);

        session()->flash('message', 'Tingkatan perjalanan berhasil ditambahkan');
        return redirect()->route('travel-grades.index');
    }

    public function render()
    {
        return view('livewire.travel-grades.create');
    }
}

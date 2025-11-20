<?php

namespace App\Livewire\Positions;

use App\Models\Position;
use App\Models\Echelon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Tambah Jabatan'])]
class Create extends Component
{
    public $name = '';
    public $type = '';
    public $echelon_id = '';

    // Mutators to handle empty strings for foreign key fields
    public function setEchelonIdProperty($value)
    {
        $this->echelon_id = ($value === '' || $value === null) ? null : $value;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'echelon_id' => 'nullable|exists:echelons,id',
        ];
    }

    public function save()
    {
        $validated = $this->validate();
        
        // Convert empty string to null for echelon_id
        if (isset($validated['echelon_id']) && $validated['echelon_id'] === '') {
            $validated['echelon_id'] = null;
        }
        
        Position::create($validated);
        
        session()->flash('message', 'Data jabatan berhasil ditambahkan.');
        
        return redirect()->route('positions.index');
    }

    public function render()
    {
        $echelons = Echelon::orderBy('code', 'asc')->get(); // I.a (tertinggi) first
        
        return view('livewire.positions.create', compact('echelons'));
    }
}

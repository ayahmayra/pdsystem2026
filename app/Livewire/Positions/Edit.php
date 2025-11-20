<?php

namespace App\Livewire\Positions;

use App\Models\Position;
use App\Models\Echelon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Edit Jabatan'])]
class Edit extends Component
{
    public Position $position;
    public $name = '';
    public $type = '';
    public $echelon_id = '';

    // Mutators to handle empty strings for foreign key fields
    public function setEchelonIdProperty($value)
    {
        $this->echelon_id = ($value === '' || $value === null) ? null : $value;
    }

    public function mount(Position $position)
    {
        $this->position = $position;
        $this->name = $position->name;
        $this->type = $position->type;
        $this->echelon_id = $position->echelon_id;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'echelon_id' => 'nullable|exists:echelons,id',
        ];
    }

    public function update()
    {
        $validated = $this->validate();
        
        // Convert empty string to null for echelon_id
        if (isset($validated['echelon_id']) && $validated['echelon_id'] === '') {
            $validated['echelon_id'] = null;
        }
        
        $this->position->update($validated);
        
        session()->flash('message', 'Data jabatan berhasil diperbarui.');
        
        return redirect()->route('positions.index');
    }

    public function render()
    {
        $echelons = Echelon::orderBy('code', 'asc')->get(); // I.a (tertinggi) first
        
        return view('livewire.positions.edit', compact('echelons'));
    }
}

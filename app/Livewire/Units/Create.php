<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Tambah Unit'])]
class Create extends Component
{
    public $code = '';
    public $name = '';
    public $parent_id = '';

    // Mutators to handle empty strings for foreign key fields
    public function setParentIdProperty($value)
    {
        $this->parent_id = ($value === '' || $value === null) ? null : $value;
    }

    protected function rules()
    {
        return [
            'code' => 'required|string|max:20|unique:units,code',
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:units,id',
        ];
    }

    public function save()
    {
        $validated = $this->validate();
        
        // Convert empty string to null for parent_id
        if (isset($validated['parent_id']) && $validated['parent_id'] === '') {
            $validated['parent_id'] = null;
        }
        
        Unit::create($validated);
        
        session()->flash('message', 'Data unit berhasil ditambahkan.');
        
        return redirect()->route('units.index');
    }

    public function render()
    {
        $units = Unit::orderBy('code')->get(); // For parent dropdown
        
        return view('livewire.units.create', compact('units'));
    }
}

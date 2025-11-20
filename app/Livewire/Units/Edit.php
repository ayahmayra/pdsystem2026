<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Edit Unit'])]
class Edit extends Component
{
    public Unit $unit;
    public $code = '';
    public $name = '';
    public $parent_id = '';

    // Mutators to handle empty strings for foreign key fields
    public function setParentIdProperty($value)
    {
        $this->parent_id = ($value === '' || $value === null) ? null : $value;
    }

    public function mount(Unit $unit)
    {
        $this->unit = $unit;
        $this->code = $unit->code;
        $this->name = $unit->name;
        $this->parent_id = $unit->parent_id;
    }

    protected function rules()
    {
        return [
            'code' => 'required|string|max:20|unique:units,code,' . $this->unit->id,
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:units,id',
        ];
    }

    public function update()
    {
        $validated = $this->validate();
        
        // Prevent self-reference or circular reference
        if ($validated['parent_id'] == $this->unit->id) {
            session()->flash('error', 'Unit tidak dapat menjadi parent dari dirinya sendiri.');
            return;
        }
        
        // Convert empty string to null for parent_id
        if (isset($validated['parent_id']) && $validated['parent_id'] === '') {
            $validated['parent_id'] = null;
        }
        
        $this->unit->update($validated);
        
        session()->flash('message', 'Data unit berhasil diperbarui.');
        
        return redirect()->route('units.index');
    }

    public function render()
    {
        // Exclude current unit and its descendants from parent options
        $units = Unit::where('id', '!=', $this->unit->id)
            ->orderBy('code')
            ->get()
            ->filter(function ($unit) {
                return !$this->isDescendant($unit, $this->unit);
            });
        
        return view('livewire.units.edit', compact('units'));
    }

    /**
     * Check if a unit is a descendant of the current unit
     */
    private function isDescendant(Unit $potentialDescendant, Unit $ancestor): bool
    {
        $current = $potentialDescendant;
        while ($current->parent_id) {
            if ($current->parent_id == $ancestor->id) {
                return true;
            }
            $current = $current->parent;
        }
        return false;
    }
}

<?php

namespace App\Livewire\AtCostComponents;

use App\Models\AtCostComponent;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public AtCostComponent $component;

    #[Rule('required|string|max:50')]
    public $code = '';

    #[Rule('required|string|max:255')]
    public $name = '';

    public function mount(AtCostComponent $component)
    {
        // Check if user has permission to edit reference rates
        if (!PermissionHelper::can('reference-rates.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit data.');
        }
        
        $this->component = $component;
        $this->code = $this->component->code;
        $this->name = $this->component->name;
    }

    public function save()
    {
        // Check if user has permission to edit reference rates
        if (!PermissionHelper::can('reference-rates.edit')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengedit data.');
            return;
        }
        
        $this->validate([
            'code' => 'required|string|max:50|unique:atcost_components,code,' . $this->component->id,
            'name' => 'required|string|max:255',
        ]);

        $this->component->update([
            'code' => strtoupper($this->code),
            'name' => $this->name,
        ]);

        session()->flash('message', 'Komponen at-cost berhasil diperbarui');
        return $this->redirect(route('at-cost-components.index'));
    }

    public function render()
    {
        return view('livewire.at-cost-components.edit');
    }
}

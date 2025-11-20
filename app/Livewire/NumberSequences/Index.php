<?php

namespace App\Livewire\NumberSequences;

use App\Models\NumberSequence;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app', ['title' => 'Number Sequence'])]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $editId = null;
    public $editValue = null;

    public function updatingSearch() { $this->resetPage(); }

    public function startEdit($id, $value)
    {
        $this->editId = $id;
        $this->editValue = $value;
    }

    public function saveEdit(NumberSequence $sequence)
    {
        $this->validate(['editValue' => 'required|integer|min:1']);
        $sequence->update(['current_value' => $this->editValue]);
        $this->editId = null;
        $this->editValue = null;
        session()->flash('message', 'Sequence berhasil diupdate.');
    }

    public function render()
    {
        $sequences = NumberSequence::with('unitScope')
            ->when($this->search, function($q) {
                $q->where('doc_type', 'like', '%'.$this->search.'%');
            })
            ->orderBy('doc_type')
            ->orderBy('unit_scope_id')
            ->orderByDesc('year_scope')
            ->orderByDesc('month_scope')
            ->paginate(15);
        return view('livewire.number-sequences.index', [
            'sequences' => $sequences
        ]);
    }
}

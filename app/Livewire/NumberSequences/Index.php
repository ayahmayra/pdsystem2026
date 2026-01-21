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
    public $showResetModal = false;
    public $resetDocType = '';
    public $resetYear = '';
    public $resetAll = false;

    public function updatingSearch() { $this->resetPage(); }

    public function startEdit($id, $value)
    {
        $this->editId = $id;
        $this->editValue = $value;
    }

    public function saveEdit(NumberSequence $sequence)
    {
        $this->validate(['editValue' => 'required|integer|min:0']);
        $sequence->update(['current_value' => $this->editValue]);
        $this->editId = null;
        $this->editValue = null;
        session()->flash('message', 'Sequence berhasil diupdate.');
    }

    public function resetSequence($id)
    {
        $sequence = NumberSequence::find($id);
        if ($sequence) {
            $sequence->update([
                'current_value' => 0,
                'last_generated_at' => now(),
            ]);
            session()->flash('message', 'Sequence berhasil direset ke 0.');
        }
    }

    public function openResetModal()
    {
        $this->showResetModal = true;
        $this->resetDocType = '';
        $this->resetYear = '';
        $this->resetAll = false;
    }

    public function closeResetModal()
    {
        $this->showResetModal = false;
        $this->resetDocType = '';
        $this->resetYear = '';
        $this->resetAll = false;
    }

    public function resetSequences()
    {
        $query = NumberSequence::query();

        if ($this->resetAll) {
            // Reset semua
        } elseif ($this->resetDocType) {
            $query->where('doc_type', $this->resetDocType);
            if ($this->resetYear) {
                $query->where('year_scope', $this->resetYear);
            }
        } elseif ($this->resetYear) {
            $query->where('year_scope', $this->resetYear);
        } else {
            session()->flash('error', 'Pilih minimal satu filter untuk reset.');
            return;
        }

        $count = $query->count();

        if ($count === 0) {
            session()->flash('error', 'Tidak ada sequence yang ditemukan dengan kriteria yang diberikan.');
            $this->closeResetModal();
            return;
        }

        $updated = $query->update([
            'current_value' => 0,
            'last_generated_at' => now(),
        ]);

        $this->closeResetModal();
        $this->resetPage();
        session()->flash('message', "Berhasil mereset {$updated} sequence(s) ke 0. Dokumen berikutnya akan dimulai dari nomor 001.");
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

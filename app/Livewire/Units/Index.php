<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['title' => 'Data Unit'])]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete(Unit $unit)
    {
        try {
            // Check if unit is being used by users
            if ($unit->users()->count() > 0) {
                session()->flash('error', 'Unit tidak dapat dihapus karena masih digunakan oleh pegawai.');
                return;
            }

            // Check if unit has children
            if ($unit->children()->count() > 0) {
                session()->flash('error', 'Unit tidak dapat dihapus karena masih memiliki sub unit.');
                return;
            }

            $unit->delete();
            session()->flash('message', 'Data unit berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data unit. ' . $e->getMessage());
        }
    }

    public function render()
    {
        $units = Unit::with(['parent', 'children'])
            ->withCount(['users', 'children'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('name', 'like', '%' . $this->search . '%')
                      ->orWhereHas('parent', function ($pq) {
                          $pq->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('code')
            ->paginate(10);

        return view('livewire.units.index', compact('units'));
    }
}

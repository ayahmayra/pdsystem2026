<?php

namespace App\Livewire\Positions;

use App\Models\Position;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['title' => 'Data Jabatan'])]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete(Position $position)
    {
        try {
            // Check if position is being used by users
            if ($position->users()->count() > 0) {
                session()->flash('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh pegawai.');
                return;
            }

            $position->delete();
            session()->flash('message', 'Data jabatan berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data jabatan. ' . $e->getMessage());
        }
    }

    public function render()
    {
        $positions = Position::with('echelon')
            ->withCount('users')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('type', 'like', '%' . $this->search . '%')
                      ->orWhereHas('echelon', function ($eq) {
                          $eq->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('code', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->leftJoin('echelons', 'positions.echelon_id', '=', 'echelons.id')
            // 1. Sort by eselon (lower number = higher eselon)
            ->orderByRaw('CASE WHEN echelons.id IS NULL THEN 999999 ELSE echelons.id END ASC')
            // 2. Sort by position name
            ->orderBy('positions.name')
            ->select('positions.*')
            ->paginate(10);

        return view('livewire.positions.index', compact('positions'));
    }
}

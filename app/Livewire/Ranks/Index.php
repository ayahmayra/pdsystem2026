<?php

namespace App\Livewire\Ranks;

use App\Models\Rank;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['title' => 'Data Pangkat'])]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete(Rank $rank)
    {
        try {
            // Check if rank is being used by users
            if ($rank->users()->count() > 0) {
                session()->flash('error', 'Pangkat tidak dapat dihapus karena masih digunakan oleh pegawai.');
                return;
            }

            $rank->delete();
            session()->flash('message', 'Data pangkat berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data pangkat. ' . $e->getMessage());
        }
    }

    public function render()
    {
        $ranks = Rank::withCount('users')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('code', 'desc') // Pangkat tertinggi (IV/e) first
            ->paginate(10);

        return view('livewire.ranks.index', compact('ranks'));
    }
}

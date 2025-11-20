<?php

namespace App\Livewire\TravelGrades;

use App\Models\TravelGrade;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete($id)
    {
        $travelGrade = TravelGrade::findOrFail($id);
        
        if ($travelGrade->userMaps()->count() > 0) {
            session()->flash('error', 'Tingkatan perjalanan tidak dapat dihapus karena masih memiliki data mapping pengguna');
            return;
        }
        
        $travelGrade->delete();
        session()->flash('message', 'Tingkatan perjalanan berhasil dihapus');
    }

    public function render()
    {
        $travelGrades = TravelGrade::query()
            ->withCount('userMaps')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.travel-grades.index', [
            'travelGrades' => $travelGrades
        ]);
    }
}

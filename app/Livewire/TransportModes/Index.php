<?php

namespace App\Livewire\TransportModes;

use App\Models\TransportMode;
use App\Helpers\PermissionHelper;
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
        // Check if user has permission to delete locations
        if (!PermissionHelper::can('locations.delete')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus moda transportasi.');
            return;
        }
        
        $transportMode = TransportMode::findOrFail($id);
        
        if ($transportMode->travelRoutes()->count() > 0) {
            session()->flash('error', 'Moda transportasi tidak dapat dihapus karena masih memiliki data rute perjalanan');
            return;
        }
        
        $transportMode->delete();
        session()->flash('message', 'Moda transportasi berhasil dihapus');
    }

    public function render()
    {
        $transportModes = TransportMode::query()
            ->withCount('travelRoutes')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.transport-modes.index', [
            'transportModes' => $transportModes
        ]);
    }
}

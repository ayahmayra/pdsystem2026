<?php

namespace App\Livewire\TravelRoutes;

use App\Models\TravelRoute;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

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
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus rute perjalanan.');
            return;
        }
        
        $travelRoute = TravelRoute::findOrFail($id);
        $travelRoute->delete();
        session()->flash('message', 'Rute perjalanan berhasil dihapus');
    }

    public function render()
    {
        $travelRoutes = TravelRoute::query()
            ->with(['originPlace', 'destinationPlace', 'transportMode'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('originPlace', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('destinationPlace', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('transportMode', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('code', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.travel-routes.index', [
            'travelRoutes' => $travelRoutes
        ]);
    }
}

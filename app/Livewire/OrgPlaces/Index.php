<?php

namespace App\Livewire\OrgPlaces;

use App\Models\OrgPlace;
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
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus kedudukan.');
            return;
        }
        
        $orgPlace = OrgPlace::findOrFail($id);
        $orgPlace->delete();
        session()->flash('message', 'Kedudukan organisasi berhasil dihapus');
    }

    public function render()
    {
        $orgPlaces = OrgPlace::query()
            ->with(['city.province', 'district'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhereHas('city', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('district', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('city.province', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.org-places.index', [
            'orgPlaces' => $orgPlaces,
        ]);
    }
}

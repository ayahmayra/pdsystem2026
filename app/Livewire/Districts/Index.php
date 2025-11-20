<?php

namespace App\Livewire\Districts;

use App\Models\District;
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
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus kecamatan.');
            return;
        }
        
        $district = District::findOrFail($id);
        
        // Check if district has org places
        if ($district->orgPlaces()->count() > 0) {
            session()->flash('error', 'Kecamatan tidak dapat dihapus karena masih memiliki data kedudukan organisasi');
            return;
        }
        
        $district->delete();
        session()->flash('message', 'Kecamatan berhasil dihapus');
    }

    public function render()
    {
        $districts = District::query()
            ->with(['city.province'])
            ->withCount('orgPlaces')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('kemendagri_code', 'like', '%' . $this->search . '%')
                      ->orWhereHas('city', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('city.province', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.districts.index', [
            'districts' => $districts,
        ]);
    }
}

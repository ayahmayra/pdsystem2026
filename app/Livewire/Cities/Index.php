<?php

namespace App\Livewire\Cities;

use App\Models\City;
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
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus kota/kabupaten.');
            return;
        }
        
        $city = City::findOrFail($id);
        
        // Check if city has districts
        if ($city->districts()->count() > 0) {
            session()->flash('error', 'Kota/Kabupaten tidak dapat dihapus karena masih memiliki data kecamatan');
            return;
        }

        // Check if city has org places
        if ($city->orgPlaces()->count() > 0) {
            session()->flash('error', 'Kota/Kabupaten tidak dapat dihapus karena masih memiliki data kedudukan organisasi');
            return;
        }
        
        $city->delete();
        session()->flash('message', 'Kota/Kabupaten berhasil dihapus');
    }

    public function render()
    {
        $cities = City::query()
            ->with(['province'])
            ->withCount(['districts', 'orgPlaces'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('kemendagri_code', 'like', '%' . $this->search . '%')
                      ->orWhereHas('province', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.cities.index', [
            'cities' => $cities,
        ]);
    }
}

<?php

namespace App\Livewire\Provinces;

use App\Models\Province;
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
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus provinsi.');
            return;
        }
        
        $province = Province::findOrFail($id);
        
        // Check if province has cities
        if ($province->cities()->count() > 0) {
            session()->flash('error', 'Provinsi tidak dapat dihapus karena masih memiliki data kota/kabupaten');
            return;
        }
        
        $province->delete();
        session()->flash('message', 'Provinsi berhasil dihapus');
    }

    public function render()
    {
        $provinces = Province::query()
            ->withCount('cities')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('kemendagri_code', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.provinces.index', [
            'provinces' => $provinces,
        ]);
    }
}

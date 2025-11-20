<?php

namespace App\Livewire\AirfareRefs;

use App\Models\AirfareRef;
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
        // Check if user has permission to delete reference rates
        if (!PermissionHelper::can('reference-rates.delete')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus data.');
            return;
        }
        $airfareRef = AirfareRef::findOrFail($id);
        $airfareRef->delete();
        session()->flash('message', 'Referensi tiket pesawat berhasil dihapus');
    }

    public function render()
    {
        $airfareRefs = AirfareRef::query()
            ->with(['originCity.province', 'destinationCity.province'])
            ->when($this->search, function ($query) {
                $query->whereHas('originCity', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('kemendagri_code', 'like', '%' . $this->search . '%');
                })->orWhereHas('destinationCity', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('kemendagri_code', 'like', '%' . $this->search . '%');
                })->orWhere('class', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.airfare-refs.index', [
            'airfareRefs' => $airfareRefs
        ]);
    }
}

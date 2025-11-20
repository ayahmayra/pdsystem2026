<?php

namespace App\Livewire\IntraProvinceTransportRefs;

use App\Models\IntraProvinceTransportRef;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app', ['title' => 'Data Referensi Transportasi Dalam Provinsi'])]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete(IntraProvinceTransportRef $transportRef)
    {
        try {
            $transportRef->delete();
            session()->flash('message', 'Referensi transportasi dalam provinsi berhasil dihapus');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus referensi transportasi. ' . $e->getMessage());
        }
    }

    public function render()
    {
        $transportRefs = IntraProvinceTransportRef::query()
            ->with(['originPlace.city.province', 'destinationCity.province'])
            ->whereHas('originPlace.city.province') // Pastikan originPlace dan city.province ada
            ->whereHas('destinationCity.province') // Pastikan destinationCity dan province ada
            ->when($this->search, function ($query) {
                $query->whereHas('originPlace', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('destinationCity', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('kemendagri_code', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.intra-province-transport-refs.index', [
            'transportRefs' => $transportRefs
        ]);
    }
}

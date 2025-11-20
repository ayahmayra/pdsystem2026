<?php

namespace App\Livewire\SubKeg;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SubKeg;
use App\Models\Unit;
use App\Helpers\PermissionHelper;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $unitFilter = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'unitFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingUnitFilter()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        // Check if user has permission to delete master data
        if (!PermissionHelper::can('master-data.delete')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus sub kegiatan.');
            return;
        }
        
        $subKeg = SubKeg::findOrFail($id);
        $subKeg->delete();
        
        session()->flash('success', 'Sub Kegiatan berhasil dihapus.');
    }

    public function viewRekening($id)
    {
        return redirect()->route('sub-keg.rekening', $id);
    }

    public function render()
    {
        $query = SubKeg::with(['unit', 'pptkUser', 'activeRekeningBelanja.receipts.lines']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode_subkeg', 'like', '%' . $this->search . '%')
                  ->orWhere('nama_subkeg', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->unitFilter) {
            $query->where('id_unit', $this->unitFilter);
        }

        $subKegiatan = $query->orderBy('kode_subkeg')->paginate($this->perPage);
        $units = Unit::orderBy('name')->get();

        // Calculate total statistics for all sub kegiatan (not just current page)
        $totalStats = $this->getTotalStatistics();

        return view('livewire.sub-keg.index', [
            'subKegiatan' => $subKegiatan,
            'units' => $units,
            'totalStats' => $totalStats,
        ]);
    }

    private function getTotalStatistics()
    {
        $query = SubKeg::with(['activeRekeningBelanja.receipts.lines']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode_subkeg', 'like', '%' . $this->search . '%')
                  ->orWhere('nama_subkeg', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->unitFilter) {
            $query->where('id_unit', $this->unitFilter);
        }

        $allSubKegiatan = $query->get();

        return [
            'total_sub_kegiatan' => $allSubKegiatan->count(),
            'total_rekening' => $allSubKegiatan->sum('jumlah_rekening'),
            'total_pagu' => $allSubKegiatan->sum('total_pagu'),
        ];
    }
}

<?php

namespace App\Livewire\Receipts;

use App\Models\Receipt;
use App\Models\Sppd;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sppdFilter = '';
    public $startDate = '';
    public $endDate = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sppdFilter' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSppdFilter()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'sppdFilter', 'startDate', 'endDate']);
        $this->resetPage();
    }

    public function deleteReceipt($receiptId)
    {
        $receipt = Receipt::find($receiptId);
        
        if ($receipt) {
            $receipt->delete();
            session()->flash('success', 'Kwitansi berhasil dihapus.');
        } else {
            session()->flash('error', 'Kwitansi tidak ditemukan.');
        }
    }

    public function render()
    {
        $query = Receipt::with([
            'sppd.spt.notaDinas',
            'sppd.subKeg',
            'payeeUser.position',
            'payeeUser.rank',
            'payeeUser.unit',
            'treasurerUser.position',
            'treasurerUser.unit',
            'travelGrade'
        ]);

        // Apply unit scope filtering for bendahara pengeluaran pembantu
        if (!\App\Helpers\PermissionHelper::canAccessAllData()) {
            $userUnitId = \App\Helpers\PermissionHelper::getUserUnitId();
            if ($userUnitId) {
                $query->whereHas('sppd.spt.notaDinas', function($q) use ($userUnitId) {
                    $q->where('requesting_unit_id', $userUnitId);
                });
            }
        }

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('receipt_no', 'like', '%' . $this->search . '%')
                  ->orWhereHas('sppd.subKeg', function ($subKegQuery) {
                      $subKegQuery->where('kode_subkeg', 'like', '%' . $this->search . '%')
                                  ->orWhere('nama_subkeg', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('payeeUser', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%')
                               ->orWhere('nip', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('sppd', function ($sppdQuery) {
                      $sppdQuery->where('doc_no', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply SPPD filter
        if ($this->sppdFilter) {
            $query->where('sppd_id', $this->sppdFilter);
        }

        // Apply date range filter
        if ($this->startDate) {
            $query->where('receipt_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('receipt_date', '<=', $this->endDate);
        }

        $receipts = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get SPPDs for filter dropdown
        $sppds = Sppd::orderBy('doc_no')->get();

        return view('livewire.receipts.index', [
            'receipts' => $receipts,
            'sppds' => $sppds,
        ]);
    }
}

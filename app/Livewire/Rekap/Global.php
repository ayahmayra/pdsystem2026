<?php

namespace App\Livewire\Rekap;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\NotaDinas;
use App\Models\Spt;
use App\Models\Sppd;
use App\Models\Receipt;
use App\Models\TripReport;
use App\Models\SupportingDocument;
use Illuminate\Support\Facades\DB;

class Global extends Component
{
    use WithPagination;

    // Filter properties
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $statusFilter = '';
    public $locationFilter = '';
    public $userFilter = '';
    public $unitFilter = '';

    // Pagination
    public $perPage = 25;

    // Data
    public $rekapData = [];
    public $totalRecords = 0;
    public $loading = false;

    // Filter options
    public $statusOptions = [
        '' => 'Semua Status',
        'complete' => 'Lengkap',
        'incomplete' => 'Belum Lengkap',
        'pending' => 'Pending',
        'draft' => 'Draft'
    ];

    public $locationOptions = [];
    public $userOptions = [];
    public $unitOptions = [];

    public function mount()
    {
        $this->loadFilterOptions();
        $this->loadRekapData();
    }

    public function loadFilterOptions()
    {
        // Load location options
        $this->locationOptions = DB::table('cities')
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        // Load user options
        $this->userOptions = DB::table('users')
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        // Load unit options
        $this->unitOptions = DB::table('units')
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->loadRekapData();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
        $this->loadRekapData();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
        $this->loadRekapData();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
        $this->loadRekapData();
    }

    public function updatedLocationFilter()
    {
        $this->resetPage();
        $this->loadRekapData();
    }

    public function updatedUserFilter()
    {
        $this->resetPage();
        $this->loadRekapData();
    }

    public function updatedUnitFilter()
    {
        $this->resetPage();
        $this->loadRekapData();
    }

    public function loadRekapData()
    {
        $this->loading = true;

        try {
            $query = NotaDinas::with([
                'spt.sppd.receipt',
                'spt.sppd.tripReport',
                'spt.sptMembers.user',
                'originPlace',
                'destinationCity',
                'supportingDocuments'
            ]);

            // Apply filters
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('number', 'like', '%' . $this->search . '%')
                      ->orWhere('purpose', 'like', '%' . $this->search . '%')
                      ->orWhereHas('spt', function($sq) {
                          $sq->where('number', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('spt.sppd', function($sq) {
                          $sq->where('number', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('spt.sppd.receipt', function($sq) {
                          $sq->where('number', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('spt.sppd.tripReport', function($sq) {
                          $sq->where('number', 'like', '%' . $this->search . '%');
                      });
                });
            }

            if ($this->dateFrom) {
                $query->where('date', '>=', $this->dateFrom);
            }

            if ($this->dateTo) {
                $query->where('date', '<=', $this->dateTo);
            }

            if ($this->locationFilter) {
                $query->where('destination_city_id', $this->locationFilter);
            }

            if ($this->userFilter) {
                $query->whereHas('spt.sptMembers', function($q) {
                    $q->where('user_id', $this->userFilter);
                });
            }

            if ($this->unitFilter) {
                $query->whereHas('spt.sptMembers.user', function($q) {
                    $q->where('unit_id', $this->unitFilter);
                });
            }

            // Get paginated results
            $notaDinas = $query->orderBy('date', 'desc')->paginate($this->perPage);

            // Format data for display
            $this->rekapData = $notaDinas->map(function($nd) {
                return $this->formatRekapRow($nd);
            });

            $this->totalRecords = $notaDinas->total();

        } catch (\Exception $e) {
            session()->flash('error', 'Error loading data: ' . $e->getMessage());
            $this->rekapData = [];
            $this->totalRecords = 0;
        }

        $this->loading = false;
    }

    private function formatRekapRow($notaDinas)
    {
        $spt = $notaDinas->spt;
        $sppd = $spt ? $spt->sppd : null;
        $receipt = $sppd ? $sppd->receipt : null;
        $tripReport = $sppd ? $sppd->tripReport : null;

        // Get SPT members
        $sptMembers = $spt ? $spt->sptMembers->pluck('user.name')->join(', ') : '';

        // Calculate supporting documents count
        $supportingDocsCount = 0;
        $supportingDocsLinks = [];

        if ($notaDinas->supportingDocuments) {
            $supportingDocsCount += $notaDinas->supportingDocuments->count();
            $supportingDocsLinks = array_merge($supportingDocsLinks, $notaDinas->supportingDocuments->pluck('file_path')->toArray());
        }

        if ($spt && $spt->supportingDocuments) {
            $supportingDocsCount += $spt->supportingDocuments->count();
            $supportingDocsLinks = array_merge($supportingDocsLinks, $spt->supportingDocuments->pluck('file_path')->toArray());
        }

        if ($sppd && $sppd->supportingDocuments) {
            $supportingDocsCount += $sppd->supportingDocuments->count();
            $supportingDocsLinks = array_merge($supportingDocsLinks, $sppd->supportingDocuments->pluck('file_path')->toArray());
        }

        if ($receipt && $receipt->supportingDocuments) {
            $supportingDocsCount += $receipt->supportingDocuments->count();
            $supportingDocsLinks = array_merge($supportingDocsLinks, $receipt->supportingDocuments->pluck('file_path')->toArray());
        }

        if ($tripReport && $tripReport->supportingDocuments) {
            $supportingDocsCount += $tripReport->supportingDocuments->count();
            $supportingDocsLinks = array_merge($supportingDocsLinks, $tripReport->supportingDocuments->pluck('file_path')->toArray());
        }

        // Calculate overall status
        $overallStatus = $this->calculateOverallStatus($notaDinas, $spt, $sppd, $receipt, $tripReport);

        return [
            'nota_dinas_id' => $notaDinas->id,
            'nota_dinas_number' => $notaDinas->number,
            'nota_dinas_date' => $notaDinas->date,
            'nota_dinas_purpose' => $notaDinas->purpose,
            'nota_dinas_origin' => $notaDinas->originPlace ? $notaDinas->originPlace->name : '',
            'nota_dinas_destination' => $notaDinas->destinationCity ? $notaDinas->destinationCity->name : '',
            'nota_dinas_duration' => $notaDinas->duration,

            'spt_id' => $spt ? $spt->id : null,
            'spt_number' => $spt ? $spt->number : '',
            'spt_date' => $spt ? $spt->date : '',
            'spt_members_count' => $spt ? $spt->sptMembers->count() : 0,
            'spt_members_names' => $sptMembers,

            'sppd_id' => $sppd ? $sppd->id : null,
            'sppd_number' => $sppd ? $sppd->number : '',
            'sppd_date' => $sppd ? $sppd->date : '',
            'sppd_departure_date' => $sppd ? $sppd->departure_date : '',
            'sppd_return_date' => $sppd ? $sppd->return_date : '',
            'sppd_actual_duration' => $sppd ? $this->calculateActualDuration($sppd->departure_date, $sppd->return_date) : 0,

            'receipt_id' => $receipt ? $receipt->id : null,
            'receipt_number' => $receipt ? $receipt->number : '',
            'receipt_date' => $receipt ? $receipt->date : '',
            'receipt_total_amount' => $receipt ? $receipt->total_amount : 0,
            'receipt_status' => $receipt ? $receipt->status : '',

            'trip_report_id' => $tripReport ? $tripReport->id : null,
            'trip_report_number' => $tripReport ? $tripReport->number : '',
            'trip_report_date' => $tripReport ? $tripReport->date : '',
            'trip_report_status' => $tripReport ? $tripReport->status : '',

            'supporting_documents_count' => $supportingDocsCount,
            'supporting_documents_links' => $supportingDocsLinks,

            'overall_status' => $overallStatus,
        ];
    }

    private function calculateOverallStatus($notaDinas, $spt, $sppd, $receipt, $tripReport)
    {
        $statuses = [];

        // Check each document status
        if ($notaDinas) {
            $statuses[] = $notaDinas->status ?? 'draft';
        }

        if ($spt) {
            $statuses[] = $spt->status ?? 'draft';
        }

        if ($sppd) {
            $statuses[] = $sppd->status ?? 'draft';
        }

        if ($receipt) {
            $statuses[] = $receipt->status ?? 'draft';
        }

        if ($tripReport) {
            $statuses[] = $tripReport->status ?? 'draft';
        }

        // Determine overall status
        if (empty($statuses)) {
            return 'draft';
        }

        if (in_array('rejected', $statuses)) {
            return 'rejected';
        }

        if (in_array('pending', $statuses)) {
            return 'pending';
        }

        if (in_array('draft', $statuses)) {
            return 'incomplete';
        }

        if (count($statuses) >= 4 && !in_array('draft', $statuses)) {
            return 'complete';
        }

        return 'incomplete';
    }

    private function calculateActualDuration($departureDate, $returnDate)
    {
        if (!$departureDate || !$returnDate) {
            return 0;
        }

        $departure = \Carbon\Carbon::parse($departureDate);
        $return = \Carbon\Carbon::parse($returnDate);

        return $departure->diffInDays($return) + 1;
    }

    public function viewDocument($type, $id)
    {
        switch ($type) {
            case 'nota-dinas':
                return redirect()->route('nota-dinas.pdf', $id);
            case 'spt':
                return redirect()->route('spt.pdf', $id);
            case 'sppd':
                return redirect()->route('sppd.pdf', $id);
            case 'receipt':
                return redirect()->route('receipts.pdf', $id);
            case 'trip-report':
                return redirect()->route('trip-reports.pdf', $id);
            default:
                session()->flash('error', 'Document type not found');
        }
    }

    public function downloadPdf($type, $id)
    {
        switch ($type) {
            case 'nota-dinas':
                return redirect()->route('nota-dinas.pdf-download', $id);
            case 'spt':
                return redirect()->route('spt.pdf-download', $id);
            case 'sppd':
                return redirect()->route('sppd.pdf-download', $id);
            case 'receipt':
                return redirect()->route('receipts.pdf-download', $id);
            case 'trip-report':
                return redirect()->route('trip-reports.pdf-download', $id);
            default:
                session()->flash('error', 'Document type not found');
        }
    }

    public function exportPdf()
    {
        // TODO: Implement PDF export
        session()->flash('info', 'PDF export functionality will be implemented soon');
    }

    public function exportExcel()
    {
        // TODO: Implement Excel export
        session()->flash('info', 'Excel export functionality will be implemented soon');
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->statusFilter = '';
        $this->locationFilter = '';
        $this->userFilter = '';
        $this->unitFilter = '';
        $this->resetPage();
        $this->loadRekapData();
    }

    public function getPage()
    {
        return request()->get('page', 1);
    }

    public function getTotalPages()
    {
        return ceil($this->totalRecords / $this->perPage);
    }

    public function render()
    {
        return view('livewire.rekap.global', [
            'rekapData' => $this->rekapData,
            'totalRecords' => $this->totalRecords,
            'statusOptions' => $this->statusOptions,
            'locationOptions' => $this->locationOptions,
            'userOptions' => $this->userOptions,
            'unitOptions' => $this->unitOptions,
        ]);
    }
}

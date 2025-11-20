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
use App\Exports\GlobalRekapDetailedExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class GlobalRekap extends Component
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

    public function loadRekapData($exportAll = false)
    {
        $this->loading = true;

        try {
            // Start with simple query - just NotaDinas with basic relationships
            $query = NotaDinas::with([
                'originPlace',
                'destinationCity',
                'requestingUnit',
                'supportingDocuments',
                'spt.signedByUser',
                'spt.sppds.signedByUser',
                'spt.sppds.subKeg.pptkUser',
                'spt.sppds.transportModes',
                'spt.sppds.receipts.payeeUser.rank',
                'spt.sppds.receipts.lines',
                'spt.tripReport'
            ]);

            // Apply unit scope filtering for bendahara pengeluaran pembantu
            if (!\App\Helpers\PermissionHelper::canAccessAllData()) {
                $userUnitId = \App\Helpers\PermissionHelper::getUserUnitId();
                if ($userUnitId) {
                    $query->where('requesting_unit_id', $userUnitId);
                }
            }

            // Apply basic filters
            if ($this->search) {
                $searchTerm = '%' . strtolower($this->search) . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(doc_no) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(hal) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(maksud) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(dasar) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(tembusan) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(notes) LIKE ?', [$searchTerm])
                      ->orWhereHas('requestingUnit', function($unitQuery) use ($searchTerm) {
                          $unitQuery->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                      })
                      ->orWhereHas('destinationCity', function($cityQuery) use ($searchTerm) {
                          $cityQuery->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                      })
                      ->orWhereHas('participants.user', function($userQuery) use ($searchTerm) {
                          $userQuery->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                                   ->orWhereRaw('LOWER(nip) LIKE ?', [$searchTerm]);
                      })
                      ->orWhereHas('toUser', function($userQuery) use ($searchTerm) {
                          $userQuery->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                                   ->orWhereRaw('LOWER(nip) LIKE ?', [$searchTerm]);
                      })
                      ->orWhereHas('fromUser', function($userQuery) use ($searchTerm) {
                          $userQuery->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                                   ->orWhereRaw('LOWER(nip) LIKE ?', [$searchTerm]);
                      });
                });
            }

            if ($this->dateFrom) {
                $query->where('nd_date', '>=', $this->dateFrom);
            }

            if ($this->dateTo) {
                $query->where('nd_date', '<=', $this->dateTo);
            }

            if ($this->locationFilter) {
                $query->where('destination_city_id', $this->locationFilter);
            }

            // Get results (paginated or all for export)
            if ($exportAll) {
                $notaDinas = $query->orderBy('nd_date', 'desc')->get();
            } else {
                $notaDinas = $query->orderBy('nd_date', 'desc')->paginate($this->perPage);
            }

            // Format data for display - with multiple receipts per SPPD
            $rekapData = collect();
            
            foreach ($notaDinas as $nd) {
                // Get first SPPD (assuming one SPPD per SPT for now)
                $sppd = $nd->spt && $nd->spt->sppds->count() > 0 ? $nd->spt->sppds->first() : null;
                
                // Base data for all rows
                $baseData = [
                    'id' => $nd->id,
                    'number' => $nd->doc_no,
                    'date' => $nd->nd_date,
                    'purpose' => $nd->hal,
                    'maksud' => $nd->maksud,
                    'origin' => $nd->originPlace ? $nd->originPlace->name : 'N/A',
                    'destination' => $nd->destinationCity ? $nd->destinationCity->name : 'N/A',
                    'requesting_unit' => $nd->requestingUnit ? $nd->requestingUnit->name : 'N/A',
                    'start_date' => $nd->start_date,
                    'end_date' => $nd->end_date,
                    'duration' => $nd->start_date && $nd->end_date ? \Carbon\Carbon::parse($nd->start_date)->diffInDays(\Carbon\Carbon::parse($nd->end_date)) + 1 : 0,
                    'status' => $nd->status,
                    // SPT data
                    'spt_id' => $nd->spt ? $nd->spt->id : null,
                    'spt_number' => $nd->spt ? $nd->spt->doc_no : null,
                    'spt_date' => $nd->spt ? $nd->spt->spt_date : null,
                    'spt_signer' => $nd->spt && $nd->spt->signedByUser ? 
                        ($nd->spt->signedByUser->gelar_depan ? $nd->spt->signedByUser->gelar_depan . ' ' : '') .
                        $nd->spt->signedByUser->name .
                        ($nd->spt->signedByUser->gelar_belakang ? ', ' . $nd->spt->signedByUser->gelar_belakang : '') : null,
                    // SPPD data
                    'sppd_id' => $sppd ? $sppd->id : null,
                    'sppd_number' => $sppd ? $sppd->doc_no : null,
                    'sppd_date' => $sppd ? $sppd->sppd_date : null,
                    'sppd_signer' => $sppd && $sppd->signedByUser ? 
                        ($sppd->signedByUser->gelar_depan ? $sppd->signedByUser->gelar_depan . ' ' : '') .
                        $sppd->signedByUser->name .
                        ($sppd->signedByUser->gelar_belakang ? ', ' . $sppd->signedByUser->gelar_belakang : '') : null,
                    'transport_mode' => $sppd && $sppd->transportModes->count() > 0 ? 
                        $sppd->transportModes->pluck('name')->join(', ') : null,
                    'pptk_name' => $sppd && $sppd->subKeg && $sppd->subKeg->pptkUser ? 
                        ($sppd->subKeg->pptkUser->gelar_depan ? $sppd->subKeg->pptkUser->gelar_depan . ' ' : '') .
                        $sppd->subKeg->pptkUser->name .
                        ($sppd->subKeg->pptkUser->gelar_belakang ? ', ' . $sppd->subKeg->pptkUser->gelar_belakang : '') : null,
                    // Trip Report data
                    'trip_report_id' => $nd->spt && $nd->spt->tripReport ? $nd->spt->tripReport->id : null,
                    'trip_report_number' => $nd->spt && $nd->spt->tripReport ? 
                        ($nd->spt->tripReport->report_no ?: $nd->spt->tripReport->doc_no ?: 'LAP-' . $nd->spt->tripReport->id) : null,
                    'trip_report_date' => $nd->spt && $nd->spt->tripReport ? $nd->spt->tripReport->report_date : null,
                    // Supporting documents
                    'supporting_documents' => $this->getSupportingDocuments($nd),
                ];
                
                // If SPPD has receipts, create rows with proper structure
                if ($sppd && $sppd->receipts->count() > 0) {
                    $isFirstRow = true;
                    // Sort receipts by rank (highest rank first)
                    $sortedReceipts = $sppd->receipts->sortByDesc(function($receipt) {
                        if ($receipt->payeeUser && $receipt->payeeUser->rank) {
                            // Create a sortable value based on rank code
                            $code = $receipt->payeeUser->rank->code;
                            // Convert rank code to sortable number (higher rank = higher number)
                            if (strpos($code, 'IV/') === 0) {
                                return 400 + (int)substr($code, 3, 1);
                            } elseif (strpos($code, 'III/') === 0) {
                                return 300 + (int)substr($code, 4, 1);
                            } elseif (strpos($code, 'II/') === 0) {
                                return 200 + (int)substr($code, 3, 1);
                            } elseif (strpos($code, 'I/') === 0) {
                                return 100 + (int)substr($code, 2, 1);
                            } else {
                                // For special ranks like 7A, 6F, etc.
                                return 500 + (int)$code;
                            }
                        }
                        return 0; // No rank = lowest priority
                    });
                    
                    foreach ($sortedReceipts as $receipt) {
                        $groupedLines = $this->groupReceiptLinesByCategory($receipt->lines, $nd, $receipt->payeeUser->rank_id ?? null);
                        
                        // Find categories with multiple items and determine max items per category
                        $categoriesWithMultipleItems = [];
                        $maxItemsInCategory = 1;
                        
                        foreach ($groupedLines as $category => $lines) {
                            if (count($lines) > 1) {
                                $categoriesWithMultipleItems[$category] = $lines;
                                $maxItemsInCategory = max($maxItemsInCategory, count($lines));
                            }
                        }
                        
                        // Create main row with all categories (first item from each category only)
                        $mainRowLines = [];
                        foreach ($groupedLines as $category => $lines) {
                            // Only take the first item from each category
                            $mainRowLines[$category] = count($lines) > 0 ? [$lines[0]] : [];
                        }
                        
                        $mainRowData = [
                            // Receipt data
                            'receipt_id' => $receipt->id,
                            'receipt_number' => $receipt->receipt_no ?: $receipt->doc_no ?: 'KW-' . $receipt->id,
                            'receipt_date' => $receipt->receipt_date,
                            'receipt_total' => $receipt->lines->sum('line_total'),
                            'participant_name' => $receipt->payeeUser ? 
                                ($receipt->payeeUser->gelar_depan ? $receipt->payeeUser->gelar_depan . ' ' : '') .
                                $receipt->payeeUser->name .
                                ($receipt->payeeUser->gelar_belakang ? ', ' . $receipt->payeeUser->gelar_belakang : '') : null,
                            'participant_nip' => $receipt->payeeUser ? $receipt->payeeUser->nip : null,
                            'participant_rank' => $receipt->payeeUser && $receipt->payeeUser->rank ? 
                                $receipt->payeeUser->rank->fullName() : null,
                            // Only first item from each category
                            'receipt_lines' => $mainRowLines,
                        ];
                        
                        // Only show document info on first row
                        if ($isFirstRow) {
                            $mainRowData = array_merge($baseData, $mainRowData);
                            $isFirstRow = false;
                        } else {
                            // For subsequent rows, only show receipt info
                            $mainRowData = array_merge([
                                'id' => null,
                                'number' => null,
                                'date' => null,
                                'purpose' => null,
                                'maksud' => null,
                                'origin' => null,
                                'destination' => null,
                                'requesting_unit' => null,
                                'start_date' => null,
                                'end_date' => null,
                                'duration' => null,
                                'status' => null,
                                'spt_id' => null,
                                'spt_number' => null,
                                'spt_date' => null,
                                'spt_signer' => null,
                                'sppd_id' => null,
                                'sppd_number' => null,
                                'sppd_date' => null,
                                'sppd_signer' => null,
                                'transport_mode' => null,
                                'pptk_name' => null,
                                'trip_report_id' => null,
                                'trip_report_number' => null,
                                'trip_report_date' => null,
                            ], $mainRowData);
                        }
                        
                        $rekapData->push($mainRowData);
                        
                        // Create additional rows for items beyond the first in each category
                        for ($itemIndex = 1; $itemIndex < $maxItemsInCategory; $itemIndex++) {
                            $rowData = [];
                            
                            // For each category, get the item at this index (if it exists)
                            foreach ($groupedLines as $category => $lines) {
                                if (isset($lines[$itemIndex])) {
                                    $rowData[$category] = [$lines[$itemIndex]];
                                } else {
                                    $rowData[$category] = [];
                                }
                            }
                            
                            $additionalRowData = [
                                // Receipt data (without participant and receipt info for additional rows)
                                'receipt_id' => $receipt->id,
                                'receipt_number' => null, // Don't show receipt number on additional rows
                                'receipt_date' => null, // Don't show receipt date on additional rows
                                'receipt_total' => null, // Don't show receipt total on additional rows
                                'participant_name' => null, // Don't show participant name on additional rows
                                'participant_nip' => null, // Don't show participant NIP on additional rows
                                'participant_rank' => null, // Don't show participant rank on additional rows
                                // Receipt lines for this row (all categories)
                                'receipt_lines' => $rowData,
                            ];
                            
                            // For additional rows, only show receipt info
                            $additionalRowData = array_merge([
                                'id' => null,
                                'number' => null,
                                'date' => null,
                                'purpose' => null,
                                'maksud' => null,
                                'origin' => null,
                                'destination' => null,
                                'requesting_unit' => null,
                                'start_date' => null,
                                'end_date' => null,
                                'duration' => null,
                                'status' => null,
                                'spt_id' => null,
                                'spt_number' => null,
                                'spt_date' => null,
                                'spt_signer' => null,
                                'sppd_id' => null,
                                'sppd_number' => null,
                                'sppd_date' => null,
                                'sppd_signer' => null,
                                'transport_mode' => null,
                                'pptk_name' => null,
                                'trip_report_id' => null,
                                'trip_report_number' => null,
                                'trip_report_date' => null,
                                'supporting_documents' => null,
                            ], $additionalRowData);
                            
                            $rekapData->push($additionalRowData);
                        }
                    }
                } else {
                    // If no receipts, create one row with empty receipt data
                    $rekapData->push(array_merge($baseData, [
                        'receipt_id' => null,
                        'receipt_number' => null,
                        'receipt_date' => null,
                        'receipt_total' => null,
                        'participant_name' => null,
                        'participant_nip' => null,
                        'participant_rank' => null,
                        'receipt_lines' => [
                            'transport' => [],
                            'lodging' => [],
                            'perdiem' => [],
                            'representation' => [],
                            'other' => []
                        ],
                    ]));
                }
            }
            
            $this->rekapData = $rekapData;

            // Handle totalRecords for both paginated and non-paginated results
            if ($exportAll) {
                $this->totalRecords = $notaDinas->count();
            } else {
                $this->totalRecords = $notaDinas->total();
            }

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
        $sppd = $spt && $spt->sppds->count() > 0 ? $spt->sppds->first() : null;
        $receipt = $sppd ? $sppd->receipt : null;
        $tripReport = $spt ? $spt->tripReport : null;

        // Get SPT members
        $sptMembers = $spt ? $spt->members->pluck('user.name')->join(', ') : '';

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
            'spt_members_count' => $spt ? $spt->members->count() : 0,
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
            'trip_report_number' => $tripReport ? $tripReport->report_no : '',
            'trip_report_date' => $tripReport ? $tripReport->report_date : '',
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
        try {
            // Load all data without pagination for export
            $this->loadRekapData(true); // Pass true to load all data
            
            $fileName = 'rekap-global-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
            
            return Excel::download(new GlobalRekapDetailedExport($this->rekapData), $fileName);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengexport data ke Excel: ' . $e->getMessage());
            return;
        }
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

    public function nextPage()
    {
        if ($this->getPage() < $this->getTotalPages()) {
            $this->setPage($this->getPage() + 1);
            $this->loadRekapData();
        }
    }

    public function previousPage()
    {
        if ($this->getPage() > 1) {
            $this->setPage($this->getPage() - 1);
            $this->loadRekapData();
        }
    }

    /**
     * Group receipt lines by category for display
     */
    private function groupReceiptLinesByCategory($lines, $notaDinas, $travelGradeId)
    {
        $grouped = [
            'transport' => [],
            'lodging' => [],
            'perdiem' => [],
            'representation' => [],
            'other' => []
        ];

        foreach ($lines as $line) {
            $category = $this->getReceiptLineCategory($line->component);
            
            $lineData = [
                'component' => $line->component,
                'desc' => $line->desc,
                'qty' => $line->qty,
                'unit_amount' => $line->unit_amount,
                'line_total' => $line->line_total,
                'no_lodging' => $line->no_lodging,
                'destination_city_id' => $line->destination_city_id,
            ];

            // Add reference rate for lodging lines (use snapshot if available, otherwise calculate)
            if ($category === 'lodging') {
                $lineData['reference_rate'] = $line->reference_rate_snapshot ?? $this->getLodgingReferenceRate($notaDinas, $travelGradeId);
            }

            $grouped[$category][] = $lineData;
        }

        return $grouped;
    }

    /**
     * Get category for receipt line component
     */
    private function getReceiptLineCategory($component)
    {
        // Transport components
        if (in_array($component, ['AIRFARE', 'INTRA_PROV', 'INTRA_DISTRICT', 'OFFICIAL_VEHICLE', 'TAXI', 'RORO', 'TOLL', 'PARKIR_INAP'])) {
            return 'transport';
        }
        
        // Lodging components
        if (in_array($component, ['LODGING', 'HOTEL', 'PENGINAPAN', 'WISMA', 'ASRAMA'])) {
            return 'lodging';
        }
        
        // Perdiem
        if ($component === 'PERDIEM') {
            return 'perdiem';
        }
        
        // Representation
        if ($component === 'REPRESENTASI') {
            return 'representation';
        }
        
        // Other costs
        return 'other';
    }

    /**
     * Get reference rate for lodging calculation
     */
    private function getLodgingReferenceRate($notaDinas, $travelGradeId)
    {
        if (!$notaDinas || !$notaDinas->destinationCity || !$travelGradeId) {
            return null;
        }

        $referenceRateService = new \App\Services\ReferenceRateService();
        return $referenceRateService->getLodgingCap(
            $notaDinas->destinationCity->province_id, 
            $travelGradeId
        );
    }

    private function getSupportingDocuments($notaDinas)
    {
        $documents = collect();
        
        // Get supporting documents from Nota Dinas
        if ($notaDinas && $notaDinas->supportingDocuments) {
            foreach ($notaDinas->supportingDocuments as $doc) {
                $documents->push([
                    'name' => $doc->title ?: $doc->file_name, // Use title if available, fallback to file_name
                    'file_path' => $doc->file_path,
                    'file_name' => $doc->file_name,
                    'document_type' => $doc->document_type ?: 'Nota Dinas',
                    'file_size' => $doc->file_size,
                    'mime_type' => $doc->mime_type
                ]);
            }
        }
        
        return $documents;
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

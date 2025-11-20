<?php

namespace App\Livewire\Receipts;

use App\Models\Receipt;
use App\Models\Sppd;
use App\Models\User;
use App\Services\DocumentNumberService;
use App\Services\ReferenceRateService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public $sppd_id = null;
    public $spt_id = null;
    public $sppd = null;
    public $spt = null;


    #[Rule('required|exists:users,id')]
    public $payee_user_id = '';

    #[Rule('required|exists:rekening_belanja,id')]
    public $rekening_belanja_id = '';

    #[Rule('required|exists:users,id')]
    public $treasurer_user_id = '';

    #[Rule('required|in:Bendahara Pengeluaran,Bendahara Pengeluaran Pembantu')]
    public $treasurer_title = '';

    #[Rule('required|date')]
    public $receipt_date = '';

    public $receipt_no = '';

    #[Rule('required|exists:travel_grades,id')]
    public $travel_grade_id = '';

    // Available users for selection
    public $approvalUsers = [];
    public $treasurerUsers = [];
    public $availableParticipants = [];
    
    // Available SPPDs for selection
    public $availableSppds = [];
    
    // Available rekening belanja for selection
    public $availableRekeningBelanja;

    // Perhitungan biaya properties
    public $perdiemLines = [];
    public $transportLines = [];
    public $lodgingLines = [];
    public $representationLines = [];
    public $otherLines = [];
    public $totalAmount = 0;

    // Computed properties for reference rates
    public $airfareRate = null;
    public $airfareOrigin = '';
    public $airfareDestination = '';
    public $lodgingCap = null;
    public $lodgingProvince = '';
    public $perdiemDailyRate = null;
    public $perdiemTotalAmount = null;
    public $perdiemProvince = '';
    public $perdiemTripType = '';
    
    // Available cities for lodging destinations
    public $availableCities = [];
    public $representationRate = null;
    public $representationTripType = '';
    public $transportIntraProvince = null;
    public $transportIntraDistrict = null;

    // Validation properties for excessive values
    public $hasExcessiveValues = false;
    public $excessiveValueDetails = [];

    // Travel grade properties
    public $hasTravelGradeWarning = false;
    public $travelGradeWarningMessage = '';

    public function mount($sppd_id = null): void
    {
        $this->sppd_id = $sppd_id ?? request()->query('sppd_id');
        $this->spt_id = request()->query('spt_id');
        
        // Initialize collections
        $this->availableRekeningBelanja = collect();
        
        // If we have spt_id but no sppd_id, load available SPPDs
        if ($this->spt_id && !$this->sppd_id) {
            $this->loadAvailableSppds();
            return;
        }
        
        // If we have sppd_id, load the SPPD
        if ($this->sppd_id) {
            $this->loadSppdData();
        } else {
            session()->flash('error', 'SPPD ID atau SPT ID diperlukan untuk membuat kwitansi');
            $this->redirect(route('documents'));
            return;
        }
    }

    public function loadAvailableSppds()
    {
        $this->spt = \App\Models\Spt::with(['notaDinas.participants.user', 'sppds'])->findOrFail($this->spt_id);
        
        // Get all SPPDs for this SPT
        $allSppds = $this->spt->sppds()->with(['spt.notaDinas.participants.user'])->get();
        
        // Filter SPPDs that have participants without receipts
        $this->availableSppds = $allSppds->filter(function ($sppd) {
            // Get all participants from Nota Dinas
            $allParticipants = $sppd->spt->notaDinas->participants;
            
            // Get participants who already have receipts for this SPPD
            $participantsWithReceipts = Receipt::where('sppd_id', $sppd->id)
                ->pluck('payee_user_id')
                ->toArray();
            
            // Check if there are participants without receipts
            $availableParticipants = $allParticipants->filter(function ($participant) use ($participantsWithReceipts) {
                return !in_array($participant->user_id, $participantsWithReceipts);
            });
            
            return $availableParticipants->count() > 0;
        });
        
        if (empty($this->availableSppds)) {
            session()->flash('error', 'Tidak ada SPPD yang tersedia untuk dibuatkan kwitansi');
            $this->redirect(route('documents'));
            return;
        }
        
        // Set default receipt date
        $this->receipt_date = now()->format('Y-m-d');
        
        // Load users for approval and treasurer
        $this->loadUsers();
    }

    public function loadSppdData()
    {
        $this->sppd = Sppd::with([
            'spt.notaDinas.participants.user.travelGrade',
            'spt.notaDinas.requestingUnit',
            'spt.notaDinas.fromUser.position',
            'spt.notaDinas.toUser.position',
            'spt.notaDinas.destinationCity.province',
            'spt.signedByUser.position',
            'signedByUser.position',
            'subKeg.pptkUser.position',
            'subKeg.activeRekeningBelanja.receipts.lines'
        ])->findOrFail($this->sppd_id);
        
        // Set default receipt date
        $this->receipt_date = now()->format('Y-m-d');
        
        // Load available participants (those who don't have receipts yet)
        $this->loadAvailableParticipants();
        
        // Load rekening belanja based on sub kegiatan
        $this->loadRekeningBelanja();
        
        // Check if there are available participants for this SPPD
        if (empty($this->availableParticipants)) {
            session()->flash('error', 'SPPD ini sudah memiliki kwitansi untuk semua peserta');
            $this->redirect(route('documents'));
            return;
        }
        
        // Auto-fill fields from existing receipt if available
        $this->autoFillFromExistingReceipt();
        
        // ✅ MEMASTIKAN: Travel grade selalu dari snapshot participant
        $this->ensureTravelGradeFromSnapshot();
        
        // Load users for approval and treasurer
        $this->loadUsers();
        
        // Load available cities for lodging destinations
        $this->loadAvailableCities();
    }

    public function loadAvailableParticipants()
    {
        if (!$this->sppd || !$this->sppd->spt || !$this->sppd->spt->notaDinas) {
            $this->availableParticipants = [];
            return;
        }

        // Get all participants from Nota Dinas
        $allParticipants = $this->sppd->spt->notaDinas->participants;

        // Get participants who already have receipts for this SPPD
        $participantsWithReceipts = Receipt::where('sppd_id', $this->sppd_id)
            ->pluck('payee_user_id')
            ->toArray();

        // Filter out participants who already have receipts and sort them
        $filteredParticipants = $allParticipants->filter(function ($participant) use ($participantsWithReceipts) {
            return !in_array($participant->user_id, $participantsWithReceipts);
        })->sort(function ($a, $b) {
            // 1. Sort by eselon (position_echelon_id) - lower number = higher eselon
            $ea = $a->user_position_echelon_id_snapshot ?? $a->user?->position?->echelon?->id ?? 999999;
            $eb = $b->user_position_echelon_id_snapshot ?? $b->user?->position?->echelon?->id ?? 999999;
            if ($ea !== $eb) return $ea <=> $eb;
            
            // 2. Sort by rank (rank_id) - higher number = higher rank
            $ra = $a->user_rank_id_snapshot ?? $b->user?->rank?->id ?? 0;
            $rb = $b->user_rank_id_snapshot ?? $b->user?->rank?->id ?? 0;
            if ($ra !== $rb) return $rb <=> $ra; // DESC order for rank
            
            // 3. Sort by NIP (alphabetical)
            $na = (string)($a->user_nip_snapshot ?? $a->user?->nip ?? '');
            $nb = (string)($b->user_nip_snapshot ?? $b->user?->nip ?? '');
            return strcmp($na, $nb);
        })->values();

        // Convert to simple array for Livewire serialization
        $this->availableParticipants = $filteredParticipants->map(function ($participant) {
            return [
                'user_id' => $participant->user_id,
                'user_name_snapshot' => $participant->user_name_snapshot ?? $participant->user?->name ?? 'N/A',
                'user_position_name_snapshot' => $participant->user_position_name_snapshot ?? $participant->user?->position?->name ?? 'N/A',
                'user_rank_name_snapshot' => $participant->user_rank_name_snapshot ?? $participant->user?->rank?->name ?? 'N/A',
                'user_nip_snapshot' => $participant->user_nip_snapshot ?? $participant->user?->nip ?? 'N/A',
                'user_travel_grade_id_snapshot' => $participant->user_travel_grade_id_snapshot,
                'user_travel_grade_id_fallback' => $participant->user?->travel_grade_id, // Fallback ke user profile
            ];
        })->toArray();

        // Set default payee if available
        if (count($this->availableParticipants) > 0 && empty($this->payee_user_id)) {
            $firstParticipant = $this->availableParticipants[0];
            $this->payee_user_id = $firstParticipant['user_id'];
            
            // ✅ PRIORITAS UTAMA: Set travel grade dari snapshot participant, fallback ke user profile
            $this->travel_grade_id = $firstParticipant['user_travel_grade_id_snapshot'] ?? $firstParticipant['user_travel_grade_id_fallback'] ?? '';
            
            // Check travel grade warning
            $this->checkTravelGradeWarning($firstParticipant);
        }
    }

    public function loadRekeningBelanja()
    {
        if ($this->sppd && $this->sppd->sub_keg_id && $this->sppd->subKeg) {
            // Use the already loaded relationship
            $this->availableRekeningBelanja = $this->sppd->subKeg->activeRekeningBelanja;
        } else {
            $this->availableRekeningBelanja = collect();
        }
    }

    public function loadUsers()
    {
        // Treasurer users are now loaded directly in the view using searchableSelect
        $this->treasurerUsers = collect(); // Empty collection since we load directly in view
    }
    
    public function loadAvailableCities()
    {
        $this->availableCities = \App\Models\City::with('province')
            ->orderBy('province_id')
            ->orderBy('name')
            ->get();
    }
    

    public function autoFillFromExistingReceipt()
    {
        // Get the first existing receipt for this SPPD to auto-fill common fields
        $existingReceipt = Receipt::where('sppd_id', $this->sppd_id)->first();
        
        if ($existingReceipt) {
            // Auto-fill common fields that are usually the same for receipts in the same SPPD
            $this->treasurer_user_id = $existingReceipt->treasurer_user_id ?? $this->treasurer_user_id;
            $this->treasurer_title = $existingReceipt->treasurer_title ?? $this->treasurer_title;
            $this->receipt_date = $existingReceipt->receipt_date ?? $this->receipt_date;
            $this->rekening_belanja_id = $existingReceipt->rekening_belanja_id ?? $this->rekening_belanja_id;
            
            // ❌ JANGAN override travel_grade_id dari existing receipt
            // Travel grade harus selalu mengikuti snapshot participant
            // $this->travel_grade_id = $existingReceipt->travel_grade_id ?? $this->travel_grade_id;
        }
    }

    public function updatedPayeeUserId($value)
    {
        if ($value && $this->availableParticipants) {
            // Find the selected participant from available participants
            $selectedParticipant = collect($this->availableParticipants)
                ->where('user_id', $value)
                ->first();
            
            if ($selectedParticipant) {
                // ✅ PRIORITAS UTAMA: Update travel grade dari snapshot participant, fallback ke user profile
                $this->travel_grade_id = $selectedParticipant['user_travel_grade_id_snapshot'] ?? $selectedParticipant['user_travel_grade_id_fallback'] ?? '';
                
                // Check travel grade warning
                $this->checkTravelGradeWarning($selectedParticipant);
            }
        }
    }



    public function selectSppd($sppdId)
    {
        $this->sppd_id = $sppdId;
        $this->loadSppdData();
    }

    // Method untuk mengelola perhitungan biaya
    public function addPerdiemLine()
    {
        // Calculate days count from start_date and end_date in Nota Dinas
        $daysCount = 1; // Default value
        if ($this->sppd?->spt?->notaDinas?->start_date && $this->sppd?->spt?->notaDinas?->end_date) {
            $startDate = \Carbon\Carbon::parse($this->sppd->spt->notaDinas->start_date);
            $endDate = \Carbon\Carbon::parse($this->sppd->spt->notaDinas->end_date);
            $daysCount = $startDate->diffInDays($endDate) + 1; // +1 to include both start and end dates
        }
        
        $this->perdiemLines[] = [
            'category' => 'per_diem', // Set default category for per diem lines
            'qty' => $daysCount, // Auto-fill with travel days from Nota Dinas
            'unit_amount' => 0,
            'rate_info' => '',
            'has_reference' => false,
            'original_reference_rate' => 0,
            'is_overridden' => false,
            'exceeds_reference' => false,
            'excess_amount' => 0,
            'excess_percentage' => 0,
        ];
        
        // Auto-fill the newly added perdiem line
        $index = count($this->perdiemLines) - 1;
        $this->autoFillPerdiemRate($index);
    }

    public function removePerdiemLine($index)
    {
        unset($this->perdiemLines[$index]);
        $this->perdiemLines = array_values($this->perdiemLines);
        $this->calculateTotal();
    }

    public function addTransportLine()
    {
        $this->transportLines[] = [
            'component' => '',
            'category' => 'transport', // Set default category for transport lines
            'desc' => '',
            'qty' => 1,
            'unit_amount' => 0,
            'rate_info' => '',
            'has_reference' => false,
            'original_reference_rate' => 0,
            'is_overridden' => false,
            'exceeds_reference' => false,
            'excess_amount' => 0,
            'excess_percentage' => 0,
        ];
    }

    public function removeTransportLine($index)
    {
        unset($this->transportLines[$index]);
        $this->transportLines = array_values($this->transportLines);
        $this->calculateTotal();
    }

    public function addLodgingLine()
    {
        $this->lodgingLines[] = [
            'category' => 'lodging', // Set default category for lodging lines
            'component' => 'LODGING', // Default component
            'desc' => '',
            'qty' => 1,
            'unit_amount' => 0,
            'no_lodging' => false, // Checkbox "tidak menginap"
            'destination_city_id' => null, // For transit destinations
            'rate_info' => '',
            'has_reference' => false,
            'original_reference_rate' => 0,
            'is_overridden' => false,
            'exceeds_reference' => false,
            'excess_amount' => 0,
            'excess_percentage' => 0,
        ];
        
        // Auto-fill the newly added lodging line
        $index = count($this->lodgingLines) - 1;
        $this->autoFillLodgingRate($index);
    }

    public function removeLodgingLine($index)
    {
        unset($this->lodgingLines[$index]);
        $this->lodgingLines = array_values($this->lodgingLines);
        $this->calculateTotal();
    }

    public function addRepresentationLine()
    {
        // Calculate days count from start_date and end_date in Nota Dinas
        $daysCount = 1; // Default value
        if ($this->sppd?->spt?->notaDinas?->start_date && $this->sppd?->spt?->notaDinas?->end_date) {
            $startDate = \Carbon\Carbon::parse($this->sppd->spt->notaDinas->start_date);
            $endDate = \Carbon\Carbon::parse($this->sppd->spt->notaDinas->end_date);
            $daysCount = $startDate->diffInDays($endDate) + 1; // +1 to include both start and end dates
        }
        
        $this->representationLines[] = [
            'category' => 'representation', // Set default category for representation lines
            'qty' => $daysCount, // Auto-fill with travel days from Nota Dinas
            'unit_amount' => 0,
            'rate_info' => '',
            'has_reference' => false,
            'original_reference_rate' => 0,
            'is_overridden' => false,
            'exceeds_reference' => false,
            'excess_amount' => 0,
            'excess_percentage' => 0,
        ];
        
        // Auto-fill the newly added representation line
        $index = count($this->representationLines) - 1;
        $this->autoFillRepresentationRate($index);
    }

    public function removeRepresentationLine($index)
    {
        unset($this->representationLines[$index]);
        $this->representationLines = array_values($this->representationLines);
        $this->calculateTotal();
    }

    public function addOtherLine()
    {
        $this->otherLines[] = [
            'category' => 'other', // Set default category for other lines
            'remark' => '',
            'desc' => '',
            'qty' => 1,
            'unit_amount' => 0,
        ];
    }

    public function removeOtherLine($index)
    {
        unset($this->otherLines[$index]);
        $this->otherLines = array_values($this->otherLines);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $total = 0;

        // Hitung total transportasi
        foreach ($this->transportLines as $line) {
            $total += (float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0);
        }

        // Hitung total penginapan
        foreach ($this->lodgingLines as $line) {
            $total += (float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0);
        }

        // Hitung total uang harian
        foreach ($this->perdiemLines as $line) {
            $total += (float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0);
        }

        // Hitung total representatif
        foreach ($this->representationLines as $line) {
            $total += (float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0);
        }

        // Hitung total biaya lainnya
        foreach ($this->otherLines as $line) {
            $total += (float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0);
        }

        $this->totalAmount = $total;
    }


    public function updatedTransportLines()
    {
        // Check if any transport component has changed and auto-fill rates
        foreach ($this->transportLines as $index => $line) {
            if (isset($line['component']) && !empty($line['component'])) {
                $this->autoFillTransportRate($index, $line['component']);
            }
        }
        
        // Untuk transport, tidak perlu mengecek excessive values karena tidak ada batasan
        // Transport dibayar sesuai harga real tanpa batasan
        
        // Check all excessive values for warning banner (excluding transport)
        $this->checkAllExcessiveValues();
        
        $this->calculateTotal();
    }

    // Method untuk handle perubahan nilai manual pada transport lines
    public function updatedTransportLinesUnitAmount($value, $key)
    {
        // Untuk transport, tidak perlu mengecek excessive values karena tidak ada batasan
        // Transport dibayar sesuai harga real tanpa batasan
        
        // Check all excessive values for warning banner (excluding transport)
        $this->checkAllExcessiveValues();
        
        $this->calculateTotal();
    }

    private function autoFillTransportRate($index, $component)
    {
        if (!$component || !$this->sppd || !$this->sppd->spt->notaDinas) {
            return;
        }

        $referenceRateService = new ReferenceRateService();
        $notaDinas = $this->sppd->spt->notaDinas;
        $destinationCity = $notaDinas->destinationCity;
        $originPlace = $notaDinas->originPlace;
        $defaultOriginCity = $referenceRateService->getDefaultOriginCity();

        $unitAmount = null;
        $rateInfo = '';

        switch ($component) {
            case 'AIRFARE':
                $unitAmount = $defaultOriginCity ? $referenceRateService->getAirfareRate(
                    $defaultOriginCity->id, 
                    $destinationCity->id
                ) : null;
                $rateInfo = $defaultOriginCity ? "Tiket Pesawat: {$defaultOriginCity->name} → {$destinationCity->name}" : '';
                break;

            case 'INTRA_PROV':
                $unitAmount = $originPlace ? $referenceRateService->getIntraProvinceTransportRate(
                    $originPlace->id, 
                    $destinationCity->id
                ) : null;
                $rateInfo = $originPlace ? "Transport Dalam Provinsi: {$originPlace->name} → {$destinationCity->name}" : '';
                break;

            case 'INTRA_DISTRICT':
                $unitAmount = $originPlace && $destinationCity->district_id ? $referenceRateService->getIntraDistrictTransportRate(
                    $originPlace->id, 
                    $destinationCity->district_id
                ) : null;
                $rateInfo = $originPlace && $destinationCity->district_id ? "Transport Dalam Kabupaten: {$originPlace->name} → {$destinationCity->district_id}" : '';
                break;

            case 'OFFICIAL_VEHICLE':
                // Kendaraan dinas biasanya flat rate atau berdasarkan jarak
                $unitAmount = 0; // User input manual
                $rateInfo = "Kendaraan Dinas - Input manual sesuai ketentuan";
                break;

            case 'TAXI':
                // Taxi biasanya flat rate atau berdasarkan ketentuan
                $unitAmount = 0; // User input manual
                $rateInfo = "Taxi - Input manual sesuai ketentuan";
                break;

            case 'RORO':
                // Kapal RORO biasanya flat rate
                $unitAmount = 0; // User input manual
                $rateInfo = "Kapal RORO - Input manual sesuai ketentuan";
                break;

            case 'TOLL':
                // Tol biasanya flat rate
                $unitAmount = 0; // User input manual
                $rateInfo = "Tol - Input manual sesuai ketentuan";
                break;

            case 'PARKIR_INAP':
                // Parkir & penginapan biasanya flat rate
                $unitAmount = 0; // User input manual
                $rateInfo = "Parkir & Penginapan - Input manual sesuai ketentuan";
                break;
        }

        // Update the transport line with auto-filled data
        if (isset($this->transportLines[$index])) {
            // Jangan override nilai manual yang sudah ada
            if (!$this->transportLines[$index]['is_overridden']) {
                $this->transportLines[$index]['unit_amount'] = $unitAmount ?? 0;
            }
            
            $this->transportLines[$index]['rate_info'] = $rateInfo;
            // Jangan ubah has_reference jika sudah di-override
            if (!$this->transportLines[$index]['is_overridden']) {
                $this->transportLines[$index]['has_reference'] = $unitAmount !== null;
            }
            $this->transportLines[$index]['original_reference_rate'] = $unitAmount ?? 0;
            
            // Jika ini auto-fill baru, reset status override
            if (!$this->transportLines[$index]['is_overridden']) {
                $this->transportLines[$index]['is_overridden'] = false;
                // Untuk transport, selalu set exceeds_reference = false karena tidak ada batasan
                $this->transportLines[$index]['exceeds_reference'] = false;
                $this->transportLines[$index]['excess_amount'] = 0;
                $this->transportLines[$index]['excess_percentage'] = 0;
            }
        }
    }

    public function overrideTransportRate($index)
    {
        if (isset($this->transportLines[$index])) {
            // Simpan nilai referensi asli sebelum di-override
            if (!isset($this->transportLines[$index]['original_reference_rate']) || $this->transportLines[$index]['original_reference_rate'] == 0) {
                $this->transportLines[$index]['original_reference_rate'] = $this->transportLines[$index]['unit_amount'];
            }
            
            $this->transportLines[$index]['has_reference'] = false;
            $this->transportLines[$index]['rate_info'] = 'Nilai diubah manual oleh user';
            $this->transportLines[$index]['is_overridden'] = true;
            
            // Reset status excessive
            $this->transportLines[$index]['exceeds_reference'] = false;
            $this->transportLines[$index]['excess_amount'] = 0;
            $this->transportLines[$index]['excess_percentage'] = 0;
        }
    }

    public function checkManualValueExceedsReference($index)
    {
        if (!isset($this->transportLines[$index]) || !$this->transportLines[$index]['is_overridden']) {
            return false;
        }

        $line = $this->transportLines[$index];
        
        // Untuk transport, tidak ada batasan - dibayar sesuai harga real
        // Selalu set exceeds_reference = false untuk transport
        $this->transportLines[$index]['exceeds_reference'] = false;
        $this->transportLines[$index]['excess_amount'] = 0;
        $this->transportLines[$index]['excess_percentage'] = 0;

        return false; // Transport tidak pernah exceeds reference
    }

    public function checkAllExcessiveValues()
    {
        $this->hasExcessiveValues = false;
        $this->excessiveValueDetails = [];

        // Transport lines tidak perlu dicek karena tidak ada batasan
        // Transport dibayar sesuai harga real tanpa batasan

        // Check lodging lines
        if ($this->lodgingCap) {
            foreach ($this->lodgingLines as $index => $line) {
                if (($line['unit_amount'] ?? 0) > $this->lodgingCap) {
                    $this->hasExcessiveValues = true;
                    $this->excessiveValueDetails[] = [
                        'type' => 'Penginapan',
                        'component' => 'LODGING',
                        'index' => $index,
                        'manual_value' => $line['unit_amount'],
                        'reference_value' => $this->lodgingCap,
                        'excess_amount' => $line['unit_amount'] - $this->lodgingCap,
                        'excess_percentage' => round((($line['unit_amount'] - $this->lodgingCap) / $this->lodgingCap) * 100, 1)
                    ];
                }
            }
        }

        // Check perdiem lines
        foreach ($this->perdiemLines as $index => $line) {
            if ($line['exceeds_reference']) {
                $this->hasExcessiveValues = true;
                $this->excessiveValueDetails[] = [
                    'type' => 'Uang Harian',
                    'component' => 'PERDIEM',
                    'index' => $index,
                    'manual_value' => $line['unit_amount'],
                    'reference_value' => $line['original_reference_rate'],
                    'excess_amount' => $line['excess_amount'],
                    'excess_percentage' => $line['excess_percentage']
                ];
            }
        }

        // Check representation lines
        foreach ($this->representationLines as $index => $line) {
            if ($line['exceeds_reference']) {
                $this->hasExcessiveValues = true;
                $this->excessiveValueDetails[] = [
                    'type' => 'Representatif',
                    'component' => 'REPRESENTASI',
                    'index' => $index,
                    'manual_value' => $line['unit_amount'],
                    'reference_value' => $line['original_reference_rate'],
                    'excess_amount' => $line['excess_amount'],
                    'excess_percentage' => $line['excess_percentage']
                ];
            }
        }

        return $this->hasExcessiveValues;
    }

    public function updatedLodgingLines($value, $index)
    {
        // If destination_city_id is updated, recalculate the rate for that specific line
        if (str_contains($index, 'destination_city_id')) {
            $lineIndex = explode('.', $index)[0];
            $this->autoFillLodgingRate($lineIndex);
        }
        
        // Auto-fill lodging rates for all lodging lines
        foreach ($this->lodgingLines as $index => $line) {
            $this->autoFillLodgingRate($index);
        }
        
        // Check if any manual values exceed reference rates
        foreach ($this->lodgingLines as $index => $line) {
            if ($line['is_overridden']) {
                $this->checkLodgingValueExceedsReference($index);
            }
        }
        
        // Check all excessive values for warning banner
        $this->checkAllExcessiveValues();
        
        $this->calculateTotal();
    }

    // Method untuk handle perubahan checkbox "tidak menginap"
    public function updatedLodgingLinesNoLodging($value, $key)
    {
        $index = explode('.', $key)[1];
        if (isset($this->lodgingLines[$index])) {
            // Update the no_lodging field
            $this->lodgingLines[$index]['no_lodging'] = (bool)$value;
            
            // Reset override status when checkbox changes
            $this->lodgingLines[$index]['is_overridden'] = false;
            $this->lodgingLines[$index]['exceeds_reference'] = false;
            $this->lodgingLines[$index]['excess_amount'] = 0;
            $this->lodgingLines[$index]['excess_percentage'] = 0;
            
            // Auto-fill with new rate based on checkbox
            $this->autoFillLodgingRate($index);
        }
        
        $this->calculateTotal();
    }

    // Method untuk handle perubahan nilai manual pada lodging lines
    public function updatedLodgingLinesUnitAmount($value, $key)
    {
        // Jika nilai berubah dan ini adalah lodging yang sudah di-override, check excessive
        $index = explode('.', $key)[1];
        if (isset($this->lodgingLines[$index]) && $this->lodgingLines[$index]['is_overridden']) {
            $this->checkLodgingValueExceedsReference($index);
            $this->checkAllExcessiveValues();
        }
        
        $this->calculateTotal();
    }

    // Method untuk handle perubahan perdiem lines
    public function updatedPerdiemLines()
    {
        // Auto-fill perdiem rates for all perdiem lines
        foreach ($this->perdiemLines as $index => $line) {
            $this->autoFillPerdiemRate($index);
        }
        
        // Check if any manual values exceed reference rates
        foreach ($this->perdiemLines as $index => $line) {
            if ($line['is_overridden']) {
                $this->checkPerdiemValueExceedsReference($index);
            }
        }
        
        // Check all excessive values for warning banner
        $this->checkAllExcessiveValues();
        
        $this->calculateTotal();
    }

    // Method untuk handle perubahan nilai manual pada perdiem lines
    public function updatedPerdiemLinesUnitAmount($value, $key)
    {
        // Jika nilai berubah dan ini adalah perdiem yang sudah di-override, check excessive
        $index = explode('.', $key)[1];
        if (isset($this->perdiemLines[$index]) && $this->perdiemLines[$index]['is_overridden']) {
            $this->checkPerdiemValueExceedsReference($index);
            $this->checkAllExcessiveValues();
        }
        
        $this->calculateTotal();
    }

    // Method untuk handle perubahan representation lines
    public function updatedRepresentationLines()
    {
        // Auto-fill representation rates for all representation lines
        foreach ($this->representationLines as $index => $line) {
            $this->autoFillRepresentationRate($index);
        }
        
        // Check for excessive values
        foreach ($this->representationLines as $index => $line) {
            if ($line['is_overridden']) {
                $this->checkRepresentationValueExceedsReference($index);
            }
        }
        
        $this->calculateTotal();
    }

    // Method untuk handle perubahan nilai manual pada representation lines
    public function updatedRepresentationLinesUnitAmount($value, $key)
    {
        // Jika nilai berubah dan ini adalah representation yang sudah di-override, check excessive
        $index = explode('.', $key)[1];
        if (isset($this->representationLines[$index]) && $this->representationLines[$index]['is_overridden']) {
            $this->checkRepresentationValueExceedsReference($index);
            $this->checkAllExcessiveValues();
        }
        
        $this->calculateTotal();
    }

    private function autoFillLodgingRate($index)
    {
        if (!$this->sppd || !$this->sppd->spt->notaDinas || !$this->travel_grade_id) {
            return;
        }

        $referenceRateService = new ReferenceRateService();
        $notaDinas = $this->sppd->spt->notaDinas;
        
        // Get destination city - use specific city if selected, otherwise use main destination
        $destinationCityId = $this->lodgingLines[$index]['destination_city_id'] ?? null;
        $destinationCity = null;
        
        if ($destinationCityId) {
            $destinationCity = \App\Models\City::find($destinationCityId);
        }
        
        if (!$destinationCity) {
            $destinationCity = $notaDinas->destinationCity;
        }

        $baseLodgingCap = $referenceRateService->getLodgingCap(
            $destinationCity->province_id, 
            $this->travel_grade_id
        );
        
        // Check if "tidak menginap" is selected
        $isNoLodging = $this->lodgingLines[$index]['no_lodging'] ?? false;
        
        if ($isNoLodging && $baseLodgingCap) {
            // 30% dari tarif maksimal penginapan
            $unitAmount = $baseLodgingCap * 0.3;
            $rateInfo = "Tidak Menginap (30% dari tarif penginapan): {$destinationCity->name}, {$destinationCity->province->name} (Grade {$this->travel_grade_id})";
        } else {
            $unitAmount = $baseLodgingCap;
            $rateInfo = $unitAmount ? "Penginapan: {$destinationCity->name}, {$destinationCity->province->name} (Grade {$this->travel_grade_id})" : '';
        }

        // Update the lodging line with auto-filled data
        if (isset($this->lodgingLines[$index])) {
            // Jangan override nilai manual yang sudah ada
            if (!$this->lodgingLines[$index]['is_overridden']) {
                $this->lodgingLines[$index]['unit_amount'] = $unitAmount ?? 0;
                $this->lodgingLines[$index]['has_reference'] = $unitAmount !== null;
                $this->lodgingLines[$index]['is_overridden'] = false;
                $this->lodgingLines[$index]['exceeds_reference'] = false;
                $this->lodgingLines[$index]['excess_amount'] = 0;
                $this->lodgingLines[$index]['excess_percentage'] = 0;
            }
            
            // Update rate info dan original reference rate (ini selalu bisa diupdate)
            $this->lodgingLines[$index]['rate_info'] = $rateInfo;
            $this->lodgingLines[$index]['original_reference_rate'] = $unitAmount ?? 0;
            $this->lodgingLines[$index]['reference_rate_snapshot'] = $baseLodgingCap ?? 0;
            
            // Preserve the no_lodging field value
            $this->lodgingLines[$index]['no_lodging'] = $isNoLodging;
        }
    }

    private function autoFillPerdiemRate($index)
    {
        if (!$this->sppd || !$this->sppd->spt->notaDinas || !$this->travel_grade_id) {
            return;
        }

        $referenceRateService = new ReferenceRateService();
        $notaDinas = $this->sppd->spt->notaDinas;
        $destinationCity = $notaDinas->destinationCity;

        // Get perdiem rate based on province, travel grade, and trip type
        $perdiemRate = $referenceRateService->getPerdiemRate(
            $destinationCity->province_id, 
            $this->travel_grade_id,
            $referenceRateService->getTripType($notaDinas)
        );
        
        $rateInfo = $perdiemRate ? "Uang Harian: {$destinationCity->province->name} (Grade {$this->travel_grade_id})" : '';

        // Update the perdiem line with auto-filled data
        if (isset($this->perdiemLines[$index])) {
            // Jangan override nilai manual yang sudah ada
            if (!$this->perdiemLines[$index]['is_overridden']) {
                $this->perdiemLines[$index]['unit_amount'] = $perdiemRate ?? 0;
                $this->perdiemLines[$index]['has_reference'] = $perdiemRate !== null;
                $this->perdiemLines[$index]['is_overridden'] = false;
                $this->perdiemLines[$index]['exceeds_reference'] = false;
                $this->perdiemLines[$index]['excess_amount'] = 0;
                $this->perdiemLines[$index]['excess_percentage'] = 0;
            }
            
            // Update rate info dan original reference rate (ini selalu bisa diupdate)
            $this->perdiemLines[$index]['rate_info'] = $rateInfo;
            $this->perdiemLines[$index]['original_reference_rate'] = $perdiemRate ?? 0;
        }
    }

    private function autoFillRepresentationRate($index)
    {
        if (!$this->sppd || !$this->sppd->spt->notaDinas || !$this->travel_grade_id) {
            return;
        }

        $referenceRateService = new ReferenceRateService();
        $notaDinas = $this->sppd->spt->notaDinas;
        $destinationCity = $notaDinas->destinationCity;

        if (!$destinationCity) {
            return;
        }

        // Get trip type from Nota Dinas
        $tripType = $referenceRateService->getTripType($notaDinas);

        // Get representation rate based on travel grade and trip type
        $representationRate = $referenceRateService->getRepresentationRate(
            $this->travel_grade_id,
            $tripType
        );

        // Create rate info
        $rateInfo = '';
        if ($representationRate !== null) {
            $rateInfo = "Representatif: {$destinationCity->province->name} (Grade {$this->travel_grade_id})";
        } else {
            $rateInfo = "Tidak ada referensi representatif untuk {$destinationCity->province->name} (Grade {$this->travel_grade_id})";
        }

        // Update the representation line with auto-filled data
        if (isset($this->representationLines[$index])) {
            // Jangan override nilai manual yang sudah ada
            if (!$this->representationLines[$index]['is_overridden']) {
                $this->representationLines[$index]['unit_amount'] = $representationRate ?? 0;
                $this->representationLines[$index]['has_reference'] = $representationRate !== null;
                $this->representationLines[$index]['is_overridden'] = false;
                $this->representationLines[$index]['exceeds_reference'] = false;
                $this->representationLines[$index]['excess_amount'] = 0;
                $this->representationLines[$index]['excess_percentage'] = 0;
            }
            
            // Update rate info dan original reference rate (ini selalu bisa diupdate)
            $this->representationLines[$index]['rate_info'] = $rateInfo;
            $this->representationLines[$index]['original_reference_rate'] = $representationRate ?? 0;
        }
    }

    public function overrideLodgingRate($index)
    {
        if (isset($this->lodgingLines[$index])) {
            // Simpan nilai referensi asli sebelum di-override
            if (!isset($this->lodgingLines[$index]['original_reference_rate']) || $this->lodgingLines[$index]['original_reference_rate'] == 0) {
                $this->lodgingLines[$index]['original_reference_rate'] = $this->lodgingLines[$index]['unit_amount'];
            }
            
            $this->lodgingLines[$index]['has_reference'] = false;
            $this->lodgingLines[$index]['rate_info'] = 'Nilai diubah manual oleh user';
            $this->lodgingLines[$index]['is_overridden'] = true;
            
            // Reset status excessive
            $this->lodgingLines[$index]['exceeds_reference'] = false;
            $this->lodgingLines[$index]['excess_amount'] = 0;
            $this->lodgingLines[$index]['excess_percentage'] = 0;
        }
    }

    public function overridePerdiemRate($index)
    {
        if (isset($this->perdiemLines[$index])) {
            // Simpan nilai referensi asli sebelum di-override
            if (!isset($this->perdiemLines[$index]['original_reference_rate']) || $this->perdiemLines[$index]['original_reference_rate'] == 0) {
                $this->perdiemLines[$index]['original_reference_rate'] = $this->perdiemLines[$index]['unit_amount'];
            }
            
            $this->perdiemLines[$index]['has_reference'] = false;
            $this->perdiemLines[$index]['rate_info'] = 'Nilai diubah manual oleh user';
            $this->perdiemLines[$index]['is_overridden'] = true;
            
            // Reset status excessive
            $this->perdiemLines[$index]['exceeds_reference'] = false;
            $this->perdiemLines[$index]['excess_amount'] = 0;
            $this->perdiemLines[$index]['excess_percentage'] = 0;
        }
    }

    public function overrideRepresentationRate($index)
    {
        if (isset($this->representationLines[$index])) {
            // Simpan nilai referensi asli sebelum di-override
            if (!isset($this->representationLines[$index]['original_reference_rate']) || $this->representationLines[$index]['original_reference_rate'] == 0) {
                $this->representationLines[$index]['original_reference_rate'] = $this->representationLines[$index]['unit_amount'];
            }
            
            $this->representationLines[$index]['has_reference'] = false;
            $this->representationLines[$index]['rate_info'] = 'Nilai diubah manual oleh user';
            $this->representationLines[$index]['is_overridden'] = true;
            
            // Reset status excessive
            $this->representationLines[$index]['exceeds_reference'] = false;
            $this->representationLines[$index]['excess_amount'] = 0;
            $this->representationLines[$index]['excess_percentage'] = 0;
        }
    }

    public function checkRepresentationValueExceedsReference($index)
    {
        if (!isset($this->representationLines[$index]) || !$this->representationLines[$index]['is_overridden']) {
            return false;
        }

        $line = $this->representationLines[$index];
        $manualValue = $line['unit_amount'];
        $referenceValue = $line['original_reference_rate'] ?? 0;

        if ($referenceValue > 0 && $manualValue > $referenceValue) {
            $excessAmount = $manualValue - $referenceValue;
            $excessPercentage = round(($excessAmount / $referenceValue) * 100, 1);
            
            $this->representationLines[$index]['exceeds_reference'] = true;
            $this->representationLines[$index]['excess_amount'] = $excessAmount;
            $this->representationLines[$index]['excess_percentage'] = $excessPercentage;
            
            return true;
        } else {
            $this->representationLines[$index]['exceeds_reference'] = false;
            $this->representationLines[$index]['excess_amount'] = 0;
            $this->representationLines[$index]['excess_percentage'] = 0;
            
            return false;
        }
    }

    public function checkLodgingValueExceedsReference($index)
    {
        if (!isset($this->lodgingLines[$index]) || !$this->lodgingLines[$index]['is_overridden']) {
            return false;
        }

        $line = $this->lodgingLines[$index];
        $manualValue = $line['unit_amount'];
        $referenceValue = $line['original_reference_rate'] ?? 0;

        if ($referenceValue > 0 && $manualValue > $referenceValue) {
            $excessAmount = $manualValue - $referenceValue;
            $excessPercentage = round(($excessAmount / $referenceValue) * 100, 1);
            
            $this->lodgingLines[$index]['exceeds_reference'] = true;
            $this->lodgingLines[$index]['excess_amount'] = $excessAmount;
            $this->lodgingLines[$index]['excess_percentage'] = $excessPercentage;
            
            return true;
        } else {
            $this->lodgingLines[$index]['exceeds_reference'] = false;
            $this->lodgingLines[$index]['excess_amount'] = 0;
            $this->lodgingLines[$index]['excess_percentage'] = 0;
            
            return false;
        }
    }

    public function checkPerdiemValueExceedsReference($index)
    {
        if (!isset($this->perdiemLines[$index]) || !$this->perdiemLines[$index]['is_overridden']) {
            return false;
        }

        $line = $this->perdiemLines[$index];
        $manualValue = $line['unit_amount'];
        $referenceValue = $line['original_reference_rate'] ?? 0;

        if ($referenceValue > 0 && $manualValue > $referenceValue) {
            $excessAmount = $manualValue - $referenceValue;
            $excessPercentage = round(($excessAmount / $referenceValue) * 100, 1);
            
            $this->perdiemLines[$index]['exceeds_reference'] = true;
            $this->perdiemLines[$index]['excess_amount'] = $excessAmount;
            $this->perdiemLines[$index]['excess_percentage'] = $excessPercentage;
            
            return true;
        } else {
            $this->perdiemLines[$index]['exceeds_reference'] = false;
            $this->perdiemLines[$index]['excess_amount'] = 0;
            $this->perdiemLines[$index]['excess_percentage'] = 0;
            
            return false;
        }
    }


    public function updatedOtherLines()
    {
        $this->calculateTotal();
    }

    public function updatedTravelGradeId()
    {
        // Auto-fill from existing receipt if available (EXCEPT travel_grade_id)
        if ($this->sppd) {
            $existingReceipt = Receipt::where('sppd_id', $this->sppd->id)->first();
            if ($existingReceipt) {
                $this->treasurer_user_id = $existingReceipt->treasurer_user_id ?: $this->treasurer_user_id;
                $this->treasurer_title = $existingReceipt->treasurer_title ?: $this->treasurer_title;
                $this->receipt_date = $existingReceipt->receipt_date ?: $this->receipt_date;
            }
        }

        // Update travel grade di user dan snapshot jika ada payee_user_id
        if ($this->travel_grade_id && $this->payee_user_id && $this->sppd) {
            $this->updateUserTravelGrade($this->payee_user_id, $this->travel_grade_id);
            $this->updateNotaDinasParticipantSnapshot($this->payee_user_id, $this->travel_grade_id);
        }

        // Calculate reference rates when travel grade is selected
        $this->calculateReferenceRates();
    }

    private function updateUserTravelGrade($userId, $travelGradeId)
    {
        $user = User::find($userId);
        if ($user && $user->travel_grade_id != $travelGradeId) {
            $user->update(['travel_grade_id' => $travelGradeId]);
        }
    }

    private function updateNotaDinasParticipantSnapshot($userId, $travelGradeId)
    {
        if (!$this->sppd || !$this->sppd->spt || !$this->sppd->spt->notaDinas) {
            return;
        }

        $notaDinas = $this->sppd->spt->notaDinas;
        $participant = $notaDinas->participants()->where('user_id', $userId)->first();
        
        if ($participant) {
            $participant->update(['user_travel_grade_id_snapshot' => $travelGradeId]);
        }
    }

    private function checkTravelGradeWarning($participant)
    {
        $this->hasTravelGradeWarning = false;
        $this->travelGradeWarningMessage = '';

        if (empty($participant['user_travel_grade_id_snapshot']) && empty($participant['user_travel_grade_id_fallback'])) {
            $this->hasTravelGradeWarning = true;
            $this->travelGradeWarningMessage = 'Peserta belum memiliki tingkat perjalanan dinas. Silakan pilih tingkat perjalanan dinas yang sesuai.';
        }
    }

    /**
     * Memastikan travel grade selalu mengikuti snapshot participant
     * Jangan pernah override dengan data dari existing receipt
     */
    private function ensureTravelGradeFromSnapshot()
    {
        if ($this->payee_user_id && $this->availableParticipants) {
            $selectedParticipant = collect($this->availableParticipants)
                ->where('user_id', $this->payee_user_id)
                ->first();
            
            if ($selectedParticipant && (!empty($selectedParticipant['user_travel_grade_id_snapshot']) || !empty($selectedParticipant['user_travel_grade_id_fallback']))) {
                // Pastikan travel grade mengikuti snapshot, fallback ke user profile
                $this->travel_grade_id = $selectedParticipant['user_travel_grade_id_snapshot'] ?? $selectedParticipant['user_travel_grade_id_fallback'];
            }
        }
    }

    /**
     * Calculate reference rates for the current trip
     */
    public function calculateReferenceRates()
    {
        if (!$this->sppd || !$this->travel_grade_id) {
            return;
        }

        $referenceRateService = new ReferenceRateService();
        $notaDinas = $this->sppd->spt->notaDinas;
        $destinationCity = $notaDinas->destinationCity;
        $originPlace = $notaDinas->originPlace;
        $tripType = $referenceRateService->getTripType($notaDinas);
        
        // Get default origin city (Pekanbaru)
        $defaultOriginCity = $referenceRateService->getDefaultOriginCity();
        
        // Set individual properties for Livewire serialization
        $this->airfareRate = $defaultOriginCity ? $referenceRateService->getAirfareRate(
            $defaultOriginCity->id, 
            $destinationCity->id
        ) : null;
        $this->airfareOrigin = $defaultOriginCity ? $defaultOriginCity->name : 'Pekanbaru';
        $this->airfareDestination = $destinationCity->name;
        
        $this->lodgingCap = $referenceRateService->getLodgingCap(
            $destinationCity->province_id, 
            $this->travel_grade_id
        );
        $this->lodgingProvince = $destinationCity->province->name ?? 'N/A';
        
        $this->perdiemDailyRate = $referenceRateService->getPerdiemRate(
            $destinationCity->province_id, 
            $this->travel_grade_id, 
            $tripType
        );
        $this->perdiemTotalAmount = $referenceRateService->calculateTotalPerdiem(
            $destinationCity->province_id, 
            $this->travel_grade_id, 
            $tripType, 
            $this->calculateTripDays($notaDinas)
        );
        $this->perdiemProvince = $destinationCity->province->name ?? 'N/A';
        $this->perdiemTripType = $tripType;
        
        $this->representationRate = $referenceRateService->getRepresentationRate(
            $this->travel_grade_id, 
            $tripType
        );
        $this->representationTripType = $tripType;
        
        $this->transportIntraProvince = $originPlace ? $referenceRateService->getIntraProvinceTransportRate(
            $originPlace->id, 
            $destinationCity->id
        ) : null;
        $this->transportIntraDistrict = $originPlace && $destinationCity->district_id ? $referenceRateService->getIntraDistrictTransportRate(
            $originPlace->id, 
            $destinationCity->district_id
        ) : null;
    }

    /**
     * Calculate trip days from start and end date
     */
    public function calculateTripDays($notaDinas)
    {
        if (!$notaDinas->start_date || !$notaDinas->end_date) {
            return 1;
        }

        $startDate = \Carbon\Carbon::parse($notaDinas->start_date);
        $endDate = \Carbon\Carbon::parse($notaDinas->end_date);
        
        return $startDate->diffInDays($endDate) + 1;
    }

    private function createReceiptLines($receipt)
    {
        // Create perdiem lines
        foreach ($this->perdiemLines as $line) {
            if (($line['qty'] ?? 0) > 0 && ($line['unit_amount'] ?? 0) > 0) {
                \App\Models\ReceiptLine::create([
                    'receipt_id' => $receipt->id,
                    'component' => 'PERDIEM',
                    'category' => $line['category'] ?? 'per_diem',
                    'qty' => $line['qty'],
                    'unit' => 'Hari',
                    'unit_amount' => $line['unit_amount'],
                    'line_total' => (float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0),
                ]);
            }
        }

        // Create transport lines
        foreach ($this->transportLines as $line) {
            if (($line['qty'] ?? 0) > 0 && ($line['unit_amount'] ?? 0) > 0 && !empty($line['component'])) {
                \App\Models\ReceiptLine::create([
                    'receipt_id' => $receipt->id,
                    'component' => $line['component'],
                    'category' => $line['category'] ?? 'transport',
                    'qty' => $line['qty'],
                    'unit' => $this->getUnitForComponent($line['component']),
                    'unit_amount' => $line['unit_amount'],
                    'desc' => $line['desc'] ?? '',
                    'line_total' => (float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0),
                ]);
            }
        }

        // Create lodging lines
        foreach ($this->lodgingLines as $line) {
            if (($line['qty'] ?? 0) > 0 && ($line['unit_amount'] ?? 0) > 0) {
                \App\Models\ReceiptLine::create([
                    'receipt_id' => $receipt->id,
                    'component' => $line['component'] ?? 'LODGING',
                    'category' => $line['category'] ?? 'lodging',
                    'qty' => $line['qty'],
                    'unit' => 'Malam',
                    'unit_amount' => $line['unit_amount'],
                    'no_lodging' => $line['no_lodging'] ?? false,
                    'reference_rate_snapshot' => $line['reference_rate_snapshot'] ?? null,
                    'destination_city_id' => $line['destination_city_id'] ?? null,
                    'desc' => $line['desc'] ?? '',
                    'line_total' => (float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0),
                ]);
            }
        }

        // Create representation lines
        foreach ($this->representationLines as $line) {
            if (($line['qty'] ?? 0) > 0 && ($line['unit_amount'] ?? 0) > 0) {
                \App\Models\ReceiptLine::create([
                    'receipt_id' => $receipt->id,
                    'component' => 'REPRESENTASI',
                    'category' => $line['category'] ?? 'representation',
                    'qty' => $line['qty'],
                    'unit' => 'Hari',
                    'unit_amount' => $line['unit_amount'],
                    'line_total' => (float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0),
                ]);
            }
        }

        // Create other lines
        foreach ($this->otherLines as $line) {
            if (($line['qty'] ?? 0) > 0 && ($line['unit_amount'] ?? 0) > 0 && !empty($line['remark'])) {
                \App\Models\ReceiptLine::create([
                    'receipt_id' => $receipt->id,
                    'component' => 'LAINNYA',
                    'category' => $line['category'] ?? 'other',
                    'qty' => $line['qty'],
                    'unit' => 'Unit',
                    'unit_amount' => $line['unit_amount'],
                    'desc' => $line['desc'] ?? '',
                    'line_total' => (float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0),
                    'remark' => $line['remark'],
                ]);
            }
        }
    }

    private function getUnitForComponent($component)
    {
        $units = [
            'AIRFARE' => 'Tiket',
            'INTRA_PROV' => 'Trip',
            'INTRA_DISTRICT' => 'Trip',
            'OFFICIAL_VEHICLE' => 'Trip',
            'TAXI' => 'Trip',
            'RORO' => 'Tiket',
            'TOLL' => 'Trip',
            'PARKIR_INAP' => 'Unit',
        ];

        return $units[$component] ?? 'Unit';
    }

    public function save()
    {
        $this->validate();

        // Check for excessive values before saving
        if ($this->checkAllExcessiveValues()) {
            session()->flash('error', 'Terdapat nilai yang melebihi standar referensi. Silakan sesuaikan terlebih dahulu sebelum menyimpan.');
            return;
        }

        // Prevent duplicate receipt for the same participant in the same SPPD
        $existingReceipt = Receipt::where('sppd_id', $this->sppd_id)
            ->where('payee_user_id', $this->payee_user_id)
            ->first();
        if ($existingReceipt) {
            session()->flash('error', 'Kwitansi untuk peserta ini sudah ada.');
            return;
        }

        // Get the selected participant
        $selectedParticipant = $this->sppd->spt->notaDinas->participants
            ->where('user_id', $this->payee_user_id)
            ->first();
        if (!$selectedParticipant) {
            session()->flash('error', 'Peserta yang dipilih tidak ditemukan dalam Nota Dinas');
            return;
        }

        // Calculate total amount
        $this->calculateTotal();

        // Create receipt with manual numbering (no automatic numbering for receipts)
        $receipt = Receipt::create([
            'doc_no' => null, // Manual numbering for receipts
            'number_is_manual' => true,
            'number_manual_reason' => 'Kwitansi menggunakan penomoran manual',
            'number_format_id' => null,
            'number_sequence_id' => null,
            'number_scope_unit_id' => null, // Not needed for manual numbering
            'sppd_id' => $this->sppd_id,
            'travel_grade_id' => $this->travel_grade_id, // Use travel grade from form
            'receipt_no' => $this->receipt_no ?: null,
            'receipt_date' => $this->receipt_date,
            'payee_user_id' => $selectedParticipant->user_id,
            'rekening_belanja_id' => $this->rekening_belanja_id,
            'treasurer_user_id' => $this->treasurer_user_id,
            'treasurer_title' => $this->treasurer_title,
            'total_amount' => $this->totalAmount,
            'status' => 'DRAFT',
        ]);

        // Create treasurer snapshot
        $receipt->createTreasurerUserSnapshot();

        // Create receipt lines
        $this->createReceiptLines($receipt);

        session()->flash('message', 'Kwitansi berhasil dibuat.');

        // Redirect back to documents page
        $this->redirect($this->getBackUrl());
    }

    public function getBackUrl()
    {
        if ($this->sppd) {
            $notaDinasId = $this->sppd->spt->nota_dinas_id;
            $sptId = $this->sppd->spt_id;
            $sppdId = $this->sppd_id;
            
            return route('documents', [
                'nota_dinas_id' => $notaDinasId,
                'spt_id' => $sptId,
                'sppd_id' => $sppdId
            ]);
        } elseif ($this->spt) {
            $notaDinasId = $this->spt->nota_dinas_id;
            $sptId = $this->spt_id;
            
            return route('documents', [
                'nota_dinas_id' => $notaDinasId,
                'spt_id' => $sptId
            ]);
        }
        
        return route('documents');
    }

    public function render()
    {
        return view('livewire.receipts.create');
    }
}

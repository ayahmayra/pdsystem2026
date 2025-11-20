<?php

namespace App\Livewire\Receipts;

use App\Models\Receipt;
use App\Models\User;
use App\Services\ReferenceRateService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public $receipt_id = null;
    public $receipt = null;


    #[Rule('required|exists:users,id')]
    public $treasurer_user_id = '';

    #[Rule('required|exists:rekening_belanja,id')]
    public $rekening_belanja_id = '';

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
    
    // Available rekening belanja for selection
    public $availableRekeningBelanja;

    // Perhitungan biaya properties
    public $perdiemLines = [];
    public $transportLines = [];
    public $lodgingLines = [];
    public $representationLines = [];
    public $otherLines = [];
    public $totalAmount = 0;

    // Transport rate tracking properties
    public $transportRateOverrides = [];

    // Validation properties for excessive values
    public $hasExcessiveValues = false;
    public $excessiveValueDetails = [];

    // Travel grade properties
    public $hasTravelGradeWarning = false;
    public $travelGradeWarningMessage = '';

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
    public $representationRate = null;
    public $representationTripType = '';
    public $transportIntraProvince = null;
    public $transportIntraDistrict = null;
    
    // Available cities for lodging destinations
    public $availableCities = [];

    public function mount($receipt_id = null): void
    {
        $this->receipt_id = $receipt_id ?? request()->route('receipt');
        
        if (!$this->receipt_id) {
            session()->flash('error', 'Kwitansi tidak ditemukan.');
            $this->redirect(route('documents'));
            return;
        }

        $this->receipt = Receipt::with(['sppd.spt.notaDinas.participants.user', 'payeeUser', 'treasurerUser', 'sppd.subKeg.activeRekeningBelanja.receipts.lines'])->findOrFail($this->receipt_id);
        
        // Prefill values
        $this->treasurer_user_id = $this->receipt->treasurer_user_id;
        $this->treasurer_title = $this->receipt->treasurer_title;
        $this->receipt_date = $this->receipt->receipt_date ?: now()->format('Y-m-d');
        $this->receipt_no = $this->receipt->receipt_no;
        $this->travel_grade_id = $this->receipt->travel_grade_id;
        $this->rekening_belanja_id = $this->receipt->rekening_belanja_id;
        
        // Load users for treasurer (bendahara) - semua user untuk searchable select
        $this->treasurerUsers = User::with(['position', 'unit'])
            ->orderBy('name')
            ->get();

        // Load available rekening belanja
        $this->loadRekeningBelanja();

        // Load existing receipt lines
        $this->loadReceiptLines();
        
        // Load available cities for lodging destinations
        $this->loadAvailableCities();
        
        // Load reference rates if travel grade is selected
        if ($this->travel_grade_id) {
            $this->loadReferenceRates();
        }

        // Check travel grade warning
        $this->checkTravelGradeWarning();
    }
    
    public function loadAvailableCities()
    {
        $this->availableCities = \App\Models\City::with('province')
            ->orderBy('province_id')
            ->orderBy('name')
            ->get();
    }

    public function loadRekeningBelanja()
    {
        if ($this->receipt && $this->receipt->sppd && $this->receipt->sppd->sub_keg_id && $this->receipt->sppd->subKeg) {
            // Use the already loaded relationship
            $this->availableRekeningBelanja = $this->receipt->sppd->subKeg->activeRekeningBelanja;
        } else {
            $this->availableRekeningBelanja = collect();
        }
    }

    private function loadReceiptLines()
    {
        $receiptLines = $this->receipt->lines()->get();
        
        foreach ($receiptLines as $line) {
            if ($line->component === 'PERDIEM') {
                // Check if this is a manual override by comparing with reference rate
                $referenceRateService = new ReferenceRateService();
                $notaDinas = $this->receipt->sppd->spt->notaDinas;
                $destinationCity = $notaDinas->destinationCity;
                $referenceRate = $referenceRateService->getPerdiemRate(
                    $destinationCity->province_id, 
                    $this->travel_grade_id,
                    $referenceRateService->getTripType($notaDinas)
                );
                
                $isOverridden = $referenceRate && $line->unit_amount != $referenceRate;
                $rateInfo = $referenceRate ? "Uang Harian: {$destinationCity->province->name} (Grade {$this->travel_grade_id})" : '';
                
                $this->perdiemLines[] = [
                    'category' => 'per_diem',
                    'qty' => $line->qty,
                    'unit_amount' => $line->unit_amount,
                    'rate_info' => $rateInfo,
                    'has_reference' => (bool)$referenceRate,
                    'original_reference_rate' => $referenceRate ?? 0,
                    'is_overridden' => $isOverridden,
                    'exceeds_reference' => $isOverridden && $line->unit_amount > $referenceRate,
                    'excess_amount' => $isOverridden && $line->unit_amount > $referenceRate ? $line->unit_amount - $referenceRate : 0,
                    'excess_percentage' => $isOverridden && $referenceRate > 0 ? (($line->unit_amount - $referenceRate) / $referenceRate) * 100 : 0,
                ];
            } elseif (in_array($line->component, ['AIRFARE', 'INTRA_PROV', 'INTRA_DISTRICT', 'OFFICIAL_VEHICLE', 'TAXI', 'RORO', 'TOLL', 'PARKIR_INAP'])) {
                $this->transportLines[] = [
                    'component' => $line->component,
                    'category' => 'transport',
                    'desc' => $line->desc ?? '',
                    'qty' => $line->qty,
                    'unit_amount' => $line->unit_amount,
                    'rate_info' => '',
                    'has_reference' => false,
                    'original_reference_rate' => 0,
                    'is_overridden' => false,
                    'exceeds_reference' => false,
                    'excess_amount' => 0,
                    'excess_percentage' => 0,
                ];
            } elseif ($line->component === 'LODGING' || in_array($line->component, ['HOTEL', 'PENGINAPAN', 'WISMA', 'ASRAMA'])) {
                // Use snapshot if available, otherwise calculate real-time
                $referenceRate = $line->reference_rate_snapshot ?? null;
                
                if (!$referenceRate) {
                    // Fallback to real-time calculation
                    $referenceRateService = new ReferenceRateService();
                    $notaDinas = $this->receipt->sppd->spt->notaDinas;
                    $destinationCity = $notaDinas->destinationCity;
                    $referenceRate = $referenceRateService->getLodgingCap(
                        $destinationCity->province_id, 
                        $this->travel_grade_id
                    );
                }
                
                // Use no_lodging field from database as primary source
                $isNoLodging = $line->no_lodging ?? false;
                
                // Check if this is a manual override
                $expectedAmount = $isNoLodging ? ($referenceRate * 0.3) : $referenceRate;
                $isOverridden = $referenceRate && $line->unit_amount != $expectedAmount;
                
                // Generate rate info based on no_lodging status
                if ($isNoLodging && $referenceRate) {
                    $rateInfo = "Tidak Menginap (30% dari tarif penginapan): {$destinationCity->name}, {$destinationCity->province->name} (Grade {$this->travel_grade_id})";
                } else {
                    $rateInfo = $referenceRate ? "Penginapan: {$destinationCity->name}, {$destinationCity->province->name} (Grade {$this->travel_grade_id})" : '';
                }
                
                $this->lodgingLines[] = [
                    'category' => 'lodging',
                    'component' => $line->component,
                    'desc' => $line->desc ?? '',
                    'qty' => $line->qty,
                    'unit_amount' => $line->unit_amount,
                    'no_lodging' => $isNoLodging,
                    'destination_city_id' => $line->destination_city_id ?? null,
                    'reference_rate_snapshot' => $line->reference_rate_snapshot ?? null,
                    'rate_info' => $rateInfo,
                    'has_reference' => (bool)$referenceRate,
                    'original_reference_rate' => $expectedAmount ?? 0,
                    'is_overridden' => $isOverridden,
                    'exceeds_reference' => $isOverridden && $line->unit_amount > $expectedAmount,
                    'excess_amount' => $isOverridden && $line->unit_amount > $expectedAmount ? $line->unit_amount - $expectedAmount : 0,
                    'excess_percentage' => $isOverridden && $expectedAmount > 0 ? (($line->unit_amount - $expectedAmount) / $expectedAmount) * 100 : 0,
                ];
            } elseif (in_array($line->component, ['REPRESENTATION', 'REPRESENTASI'])) {
                // Check if this is a manual override by comparing with reference rate
                $referenceRateService = new ReferenceRateService();
                $notaDinas = $this->receipt->sppd->spt->notaDinas;
                $destinationCity = $notaDinas->destinationCity;
                $tripType = $referenceRateService->getTripType($notaDinas);
                $referenceRate = $referenceRateService->getRepresentationRate(
                    $this->travel_grade_id,
                    $tripType
                );
                
                $isOverridden = $referenceRate && $line->unit_amount != $referenceRate;
                $rateInfo = $referenceRate ? "Representatif: {$destinationCity->province->name} (Grade {$this->travel_grade_id})" : '';
                
                $this->representationLines[] = [
                    'category' => 'representation',
                    'qty' => $line->qty,
                    'unit_amount' => $line->unit_amount,
                    'rate_info' => $rateInfo,
                    'has_reference' => (bool)$referenceRate,
                    'original_reference_rate' => $referenceRate ?? 0,
                    'is_overridden' => $isOverridden,
                    'exceeds_reference' => $isOverridden && $line->unit_amount > $referenceRate,
                    'excess_amount' => $isOverridden && $line->unit_amount > $referenceRate ? $line->unit_amount - $referenceRate : 0,
                    'excess_percentage' => $isOverridden && $referenceRate > 0 ? (($line->unit_amount - $referenceRate) / $referenceRate) * 100 : 0,
                ];
            } elseif ($line->component === 'LAINNYA') {
                $this->otherLines[] = [
                    'category' => 'other',
                    'remark' => $line->remark,
                    'desc' => $line->desc ?? '',
                    'qty' => $line->qty,
                    'unit_amount' => $line->unit_amount,
                    'no_lodging' => $line->no_lodging ?? false,
                ];
            }
        }
        
        $this->calculateTotal();
    }

    private function loadReferenceRates()
    {
        if (!$this->travel_grade_id || !$this->receipt->sppd->spt->notaDinas) {
            return;
        }

        $referenceRateService = new ReferenceRateService();
        $notaDinas = $this->receipt->sppd->spt->notaDinas;
        
        // Load airfare rate
        $this->airfareRate = $referenceRateService->getAirfareRate(
            $notaDinas->origin_city_id ?? 1, // Default to Pekanbaru
            $notaDinas->destination_city_id
        );
        
        // Get city names for display
        if ($this->airfareRate) {
            $originCity = \App\Models\City::find($notaDinas->origin_city_id ?? 1);
            $destinationCity = \App\Models\City::find($notaDinas->destination_city_id);
            $this->airfareOrigin = $originCity ? $originCity->name : 'Pekanbaru';
            $this->airfareDestination = $destinationCity ? $destinationCity->name : 'N/A';
        }

        // Load lodging cap
        $this->lodgingCap = $referenceRateService->getLodgingCap(
            $notaDinas->destination_city->province_id ?? null,
            $this->travel_grade_id
        );
        
        if ($this->lodgingCap) {
            $destinationCity = \App\Models\City::find($notaDinas->destination_city_id);
            $this->lodgingProvince = $destinationCity ? $destinationCity->province->name : 'N/A';
        }

        // Load perdiem rate
        $tripType = $referenceRateService->getTripType($notaDinas);
        $this->perdiemDailyRate = $referenceRateService->getPerdiemRate(
            $notaDinas->destination_city->province_id ?? null,
            $this->travel_grade_id,
            $tripType
        );
        
        if ($this->perdiemDailyRate) {
            $destinationCity = \App\Models\City::find($notaDinas->destination_city_id);
            $this->perdiemProvince = $destinationCity ? $destinationCity->province->name : 'N/A';
            $this->perdiemTripType = $tripType;
            
            // Calculate total perdiem amount
            $tripDays = $this->calculateTripDays($notaDinas);
            $this->perdiemTotalAmount = $this->perdiemDailyRate * $tripDays;
        }

        // Load representation rate
        $this->representationRate = $referenceRateService->getRepresentationRate(
            $this->travel_grade_id,
            $tripType
        );
        
        if ($this->representationRate) {
            $this->representationTripType = $tripType;
        }

        // Load transport rates
        $this->transportIntraProvince = $referenceRateService->getIntraProvinceTransportRate(
            $notaDinas->origin_place_id ?? 1, // Default to main office
            $notaDinas->destination_city_id
        );
        
        $this->transportIntraDistrict = $referenceRateService->getIntraDistrictTransportRate(
            $notaDinas->origin_place_id ?? 1, // Default to main office
            $notaDinas->destination_district_id ?? null
        );
    }

    private function calculateTripDays($notaDinas)
    {
        if (!$notaDinas->start_date || !$notaDinas->trip_type) {
            return 0;
        }
        
        $startDate = \Carbon\Carbon::parse($notaDinas->start_date);
        $endDate = \Carbon\Carbon::parse($notaDinas->end_date);
        
        return $startDate->diffInDays($endDate) + 1;
    }

    // Method untuk mengelola perhitungan biaya
    public function addPerdiemLine()
    {
        // Calculate days count from start_date and end_date in Nota Dinas
        $daysCount = 1; // Default value
        if ($this->receipt?->sppd?->spt?->notaDinas?->start_date && $this->receipt?->sppd?->spt?->notaDinas?->end_date) {
            $startDate = \Carbon\Carbon::parse($this->receipt->sppd->spt->notaDinas->start_date);
            $endDate = \Carbon\Carbon::parse($this->receipt->sppd->spt->notaDinas->end_date);
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
            'category' => 'transport',
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

    private function autoFillTransportRate($index, $component)
    {
        if (!$component || !$this->receipt->sppd || !$this->receipt->sppd->spt->notaDinas) {
            return;
        }

        $referenceRateService = new ReferenceRateService();
        $notaDinas = $this->receipt->sppd->spt->notaDinas;
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
                $unitAmount = 0;
                $rateInfo = "Kendaraan Dinas - Input manual sesuai ketentuan";
                break;

            case 'TAXI':
                $unitAmount = 0;
                $rateInfo = "Taxi - Input manual sesuai ketentuan";
                break;

            case 'RORO':
                $unitAmount = 0;
                $rateInfo = "Kapal RORO - Input manual sesuai ketentuan";
                break;

            case 'TOLL':
                $unitAmount = 0;
                $rateInfo = "Tol - Input manual sesuai ketentuan";
                break;

            case 'PARKIR_INAP':
                $unitAmount = 0;
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

    private function updateUserTravelGrade($userId, $travelGradeId)
    {
        $user = User::find($userId);
        if ($user && $user->travel_grade_id != $travelGradeId) {
            $user->update(['travel_grade_id' => $travelGradeId]);
        }
    }

    private function updateNotaDinasParticipantSnapshot($userId, $travelGradeId)
    {
        if (!$this->receipt || !$this->receipt->sppd || !$this->receipt->sppd->spt || !$this->receipt->sppd->spt->notaDinas) {
            return;
        }

        $notaDinas = $this->receipt->sppd->spt->notaDinas;
        $participant = $notaDinas->participants()->where('user_id', $userId)->first();
        
        if ($participant) {
            $participant->update(['user_travel_grade_id_snapshot' => $travelGradeId]);
        }
    }

    private function checkTravelGradeWarning()
    {
        if (!$this->receipt || !$this->receipt->sppd || !$this->receipt->sppd->spt || !$this->receipt->sppd->spt->notaDinas) {
            return;
        }

        $notaDinas = $this->receipt->sppd->spt->notaDinas;
        $participant = $notaDinas->participants()->where('user_id', $this->receipt->payee_user_id)->first();
        
        if ($participant && empty($participant->user_travel_grade_id_snapshot)) {
            $this->hasTravelGradeWarning = true;
            $this->travelGradeWarningMessage = 'Peserta belum memiliki tingkat perjalanan dinas. Silakan pilih tingkat perjalanan dinas yang sesuai.';
        } else {
            $this->hasTravelGradeWarning = false;
            $this->travelGradeWarningMessage = '';
        }
    }

    public function addLodgingLine()
    {
        $this->lodgingLines[] = [
            'category' => 'lodging',
            'component' => 'LODGING', // Default component
            'qty' => 1,
            'unit_amount' => 0,
            'no_lodging' => false, // Checkbox "tidak menginap"
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
        if ($this->receipt?->sppd?->spt?->notaDinas?->start_date && $this->receipt?->sppd?->spt?->notaDinas?->end_date) {
            $startDate = \Carbon\Carbon::parse($this->receipt->sppd->spt->notaDinas->start_date);
            $endDate = \Carbon\Carbon::parse($this->receipt->sppd->spt->notaDinas->end_date);
            $daysCount = $startDate->diffInDays($endDate) + 1; // +1 to include both start and end dates
        }
        
        $this->representationLines[] = [
            'category' => 'representation',
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
            'category' => 'other',
            'remark' => '',
            'qty' => 1,
            'unit_amount' => 0,
            'no_lodging' => false,
        ];
    }

    public function removeOtherLine($index)
    {
        unset($this->otherLines[$index]);
        $this->otherLines = array_values($this->otherLines);
        $this->calculateTotal();
    }

    public function updatedTravelGradeId($value)
    {
        if ($value && $this->receipt) {
            // Update travel grade di user jika berbeda dengan yang ada
            $this->updateUserTravelGrade($this->receipt->payee_user_id, $value);
            
            // Update snapshot di nota dinas participant
            $this->updateNotaDinasParticipantSnapshot($this->receipt->payee_user_id, $value);
            
            // Reload reference rates dengan travel grade baru
            $this->loadReferenceRates();
        }
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

    public function updatedLodgingLines($value, $index)
    {
        // If destination_city_id is updated, recalculate the rate for that specific line
        if (str_contains($index, 'destination_city_id')) {
            $lineIndex = explode('.', $index)[0];
            $this->autoFillLodgingRate($lineIndex);
        }
        
        // If no_lodging is updated, recalculate the rate for that specific line
        if (str_contains($index, 'no_lodging')) {
            $lineIndex = explode('.', $index)[0];
            $this->autoFillLodgingRate($lineIndex);
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

    private function autoFillLodgingRate($index)
    {
        if (!$this->receipt->sppd || !$this->receipt->sppd->spt->notaDinas || !$this->travel_grade_id) {
            return;
        }

        $referenceRateService = new ReferenceRateService();
        $notaDinas = $this->receipt->sppd->spt->notaDinas;
        
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
        if (!$this->receipt->sppd || !$this->receipt->sppd->spt->notaDinas || !$this->travel_grade_id) {
            return;
        }

        $referenceRateService = new ReferenceRateService();
        $notaDinas = $this->receipt->sppd->spt->notaDinas;
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
        if (!$this->receipt || !$this->receipt->sppd || !$this->receipt->sppd->spt->notaDinas || !$this->travel_grade_id) {
            return;
        }

        $referenceRateService = new ReferenceRateService();
        $notaDinas = $this->receipt->sppd->spt->notaDinas;
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
            
            // Pastikan reference_rate_snapshot tersimpan
            if (!isset($this->lodgingLines[$index]['reference_rate_snapshot']) || $this->lodgingLines[$index]['reference_rate_snapshot'] == 0) {
                // Recalculate reference rate snapshot
                $this->autoFillLodgingRate($index);
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

    private function updateReceiptLines()
    {
        // Delete existing receipt lines
        $this->receipt->lines()->delete();

        // Create new receipt lines
        $this->createReceiptLines($this->receipt);
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
                    'no_lodging' => $line['no_lodging'] ?? false,
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



    public function update()
    {
        $this->validate();

        // Check for excessive values before updating
        if ($this->checkAllExcessiveValues()) {
            session()->flash('error', 'Terdapat nilai yang melebihi standar referensi. Silakan sesuaikan terlebih dahulu sebelum menyimpan.');
            return;
        }

        // Store original values for comparison
        $originalTreasurerUserId = $this->receipt->treasurer_user_id;

        // Calculate total amount
        $this->calculateTotal();

        // Update receipt
        $this->receipt->update([
            'travel_grade_id' => $this->travel_grade_id,
            'treasurer_user_id' => $this->treasurer_user_id,
            'treasurer_title' => $this->treasurer_title,
            'receipt_date' => $this->receipt_date,
            'receipt_no' => $this->receipt_no ?: null,
            'total_amount' => $this->totalAmount,
            'rekening_belanja_id' => $this->rekening_belanja_id,
        ]);

        // Refresh the receipt model to get updated data
        $this->receipt->refresh();

        // Update treasurer snapshot if user changed or snapshot is missing
        if ($originalTreasurerUserId != $this->treasurer_user_id || !$this->receipt->treasurer_user_name_snapshot) {
            $this->receipt->createTreasurerUserSnapshot();
        }

        // Update receipt lines
        $this->updateReceiptLines();

        session()->flash('message', 'Kwitansi berhasil diperbarui.');

        // Redirect back to documents page
        $this->redirect($this->getBackUrl());
    }

    public function getBackUrl()
    {
        if ($this->receipt) {
            $notaDinasId = $this->receipt->sppd->spt->nota_dinas_id;
            $sptId = $this->receipt->sppd->spt_id;
            $sppdId = $this->receipt->sppd_id;
            
            return route('documents', [
                'nota_dinas_id' => $notaDinasId,
                'spt_id' => $sptId,
                'sppd_id' => $sppdId
            ]);
        }
        
        return route('documents');
    }

    public function render()
    {
        return view('livewire.receipts.edit');
    }
}

<?php

namespace App\Livewire\NotaDinas;

use App\Models\NotaDinas;
use App\Models\User;
use App\Models\Unit;
use App\Models\City;
use App\Models\OrgPlace;
use App\Services\DocumentNumberService;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Create extends Component
{
    use WithFileUploads;

    // Basic Information
    public $requesting_unit_id = '';
    public $to_user_id = '';
    public $from_user_id = '';
    public $destination_city_id = '';
    public $origin_place_id = '';
    public $sifat = 'Penting';
    public $nd_date = '';
    public $hal = '';

    // Document Number
    public $number_is_manual = false;
    public $doc_no = '';
    public $number_manual_reason = '';

    // Trip Information
    public $start_date = '';
    public $end_date = '';
    public $trip_type = 'LUAR_DAERAH';

    // Content
    public $dasar = '';
    public $maksud = '';

    // Participants
    public $participants = [];
    public $selectedUser = '';

    // Additional Information
    public $lampiran_count = 1;
    public $status = 'DRAFT';
    public $tembusan = '';
    public $notes = '';

    // Data for dropdowns
    public $units = [];
    public $users = [];
    public $cities = [];
    public $orgPlaces = [];

    // Overlap detection
    public $overlapDetails = [];
    public $showOverlapWarning = false;

    // Search
    public $search = '';
    
    // UI State
    public $isRequestingUnitDisabled = false;

    protected $rules = [
        'requesting_unit_id' => 'required|exists:units,id',
        'to_user_id' => 'required|exists:users,id',
        'from_user_id' => 'required|exists:users,id',
        'destination_city_id' => 'required|exists:cities,id',
        'origin_place_id' => 'required|exists:org_places,id',
        'sifat' => 'required|string',
        'nd_date' => 'required|date',
        'hal' => 'required|string|max:255',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'trip_type' => 'required|string',
        'dasar' => 'required|string',
        'maksud' => 'required|string',
        'participants' => 'required|array|min:1',
        'participants.*' => 'exists:users,id',
        'lampiran_count' => 'required|integer|min:1',
        'status' => 'required|string|in:DRAFT,APPROVED',
        'tembusan' => 'nullable|string',
        'notes' => 'nullable|string',
        'doc_no' => 'required_if:number_is_manual,true|string|max:255',
        'number_manual_reason' => 'required_if:number_is_manual,true|string|max:255',
    ];

    protected $messages = [
        'requesting_unit_id.required' => 'Unit pemohon harus dipilih.',
        'to_user_id.required' => 'Pegawai penerima harus dipilih.',
        'from_user_id.required' => 'Pegawai pengirim harus dipilih.',
        'destination_city_id.required' => 'Kota tujuan harus dipilih.',
        'origin_place_id.required' => 'Tempat asal harus dipilih.',
        'sifat.required' => 'Sifat surat harus dipilih.',
        'nd_date.required' => 'Tanggal nota dinas harus diisi.',
        'hal.required' => 'Hal surat harus diisi.',
        'start_date.required' => 'Tanggal mulai perjalanan harus diisi.',
        'end_date.required' => 'Tanggal selesai perjalanan harus diisi.',
        'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        'trip_type.required' => 'Jenis perjalanan harus dipilih.',
        'dasar.required' => 'Dasar surat harus diisi.',
        'maksud.required' => 'Maksud surat harus diisi.',
        'participants.required' => 'Peserta perjalanan dinas harus dipilih.',
        'participants.min' => 'Minimal harus ada 1 peserta.',
        'lampiran_count.required' => 'Jumlah lampiran harus diisi.',
        'status.required' => 'Status harus dipilih.',
        'doc_no.required_if' => 'Nomor dokumen harus diisi jika menggunakan nomor manual.',
        'number_manual_reason.required_if' => 'Alasan nomor manual harus diisi jika menggunakan nomor manual.',
    ];

    public function mount()
    {
        $this->loadData();
        $this->nd_date = now()->format('Y-m-d');
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
        
        // Auto-fill requesting_unit_id for bendahara pengeluaran pembantu
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId) {
                $this->requesting_unit_id = $userUnitId;
            }
        }
    }

    public function loadData()
    {
        // Load units - restricted for bendahara pengeluaran pembantu
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId) {
                $this->units = Unit::where('id', $userUnitId)->get();
                $this->isRequestingUnitDisabled = true;
            } else {
                $this->units = collect();
            }
        } else {
            $this->units = Unit::orderBy('name')->get();
            $this->isRequestingUnitDisabled = false;
        }
        
        $this->users = User::with(['position', 'unit'])->orderBy('name')->get();
        $this->cities = City::orderBy('name')->get();
        $this->orgPlaces = OrgPlace::orderBy('name')->get();
    }

    public function updatedParticipants()
    {
        $this->checkParticipantOverlaps();
    }

    public function updatedSelectedUser()
    {
        if ($this->selectedUser && !in_array($this->selectedUser, $this->participants)) {
            $this->participants[] = $this->selectedUser;
            $this->selectedUser = '';
            $this->checkParticipantOverlaps();
        }
    }

    public function addParticipant($userId)
    {
        if (!in_array($userId, $this->participants)) {
            $this->participants[] = $userId;
            $this->checkParticipantOverlaps();
        }
    }

    public function removeParticipant($participantId)
    {
        $this->participants = array_values(array_filter($this->participants, function($id) use ($participantId) {
            return $id != $participantId;
        }));
        $this->checkParticipantOverlaps();
    }

    public function updatedStartDate()
    {
        $this->checkParticipantOverlaps();
    }

    public function updatedEndDate()
    {
        $this->checkParticipantOverlaps();
    }

    public function checkParticipantOverlaps()
    {
        if (empty($this->participants) || empty($this->start_date) || empty($this->end_date)) {
            $this->overlapDetails = [];
            $this->showOverlapWarning = false;
            return;
        }

        $overlaps = [];
        
        foreach ($this->participants as $participantId) {
            $user = User::find($participantId);
            if (!$user) continue;

            $existingTrips = NotaDinas::whereHas('participants', function ($query) use ($participantId) {
                $query->where('user_id', $participantId);
            })
            ->where(function ($query) {
                $query->whereBetween('start_date', [$this->start_date, $this->end_date])
                      ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                      ->orWhere(function ($q) {
                          $q->where('start_date', '<=', $this->start_date)
                            ->where('end_date', '>=', $this->end_date);
                      });
            })
            ->with(['requestingUnit'])
            ->get();

            foreach ($existingTrips as $trip) {
                $overlaps[] = [
                    'user' => $user->fullNameWithTitles(),
                    'doc_no' => $trip->doc_no,
                    'unit' => $trip->requestingUnit->name ?? 'N/A',
                    'hal' => $trip->hal,
                    'start_date' => $trip->start_date,
                    'end_date' => $trip->end_date,
                ];
            }
        }

        $this->overlapDetails = $overlaps;
        $this->showOverlapWarning = !empty($overlaps);
    }

    public function store()
    {
        $this->validate();

        // Check for overlaps before saving
        $this->checkParticipantOverlaps();
        
        if ($this->showOverlapWarning) {
            $this->addError('participants', 'Terdapat konflik jadwal dengan peserta yang dipilih. Silakan periksa detail konflik di bawah.');
            return;
        }

        try {
            DB::beginTransaction();

            // Generate document number if not manual
            $docNumber = $this->doc_no;
            if (!$this->number_is_manual) {
                $numberResult = DocumentNumberService::generate('ND', $this->requesting_unit_id, $this->nd_date, [], Auth::id());
                $docNumber = $numberResult['number'];
            }

            // Create Nota Dinas
            $notaDinas = NotaDinas::create([
                'requesting_unit_id' => $this->requesting_unit_id,
                'to_user_id' => $this->to_user_id,
                'from_user_id' => $this->from_user_id,
                'destination_city_id' => $this->destination_city_id,
                'origin_place_id' => $this->origin_place_id,
                'sifat' => $this->sifat,
                'nd_date' => $this->nd_date,
                'hal' => $this->hal,
                'doc_no' => $docNumber,
                'number_is_manual' => $this->number_is_manual,
                'number_manual_reason' => $this->number_manual_reason,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'trip_type' => $this->trip_type,
                'dasar' => $this->dasar,
                'maksud' => $this->maksud,
                'lampiran_count' => $this->lampiran_count,
                'status' => $this->status,
                'tembusan' => $this->tembusan,
                'notes' => $this->notes,
                'created_by' => Auth::id(),
            ]);

            // Create participants
            foreach ($this->participants as $participantId) {
                $user = User::find($participantId);
                if ($user) {
                    $notaDinas->participants()->create([
                        'user_id' => $participantId,
                        'user_name' => $user->name,
                        'user_title' => $user->title,
                        'user_position' => $user->position?->name,
                        'user_unit' => $user->unit?->name,
                        'user_rank' => $user->rank?->name,
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', 'Nota Dinas berhasil dibuat.');
            return redirect()->route('documents');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.nota-dinas.create');
    }
}

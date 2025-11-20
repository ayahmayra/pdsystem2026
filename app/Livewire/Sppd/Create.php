<?php

namespace App\Livewire\Sppd;

use App\Models\Sppd;
use App\Models\Spt;
use App\Models\User;
use App\Models\OrgPlace;
use App\Models\City;
use App\Models\TransportMode;
use App\Models\DocNumberFormat;
use App\Models\SubKeg;
use App\Services\DocumentNumberService;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public $spt_id = null;
    public $spt = null;

    #[Rule('required|date')]
    public $sppd_date = '';

    // Bidang yang akan diterapkan ke semua SPPD yang dibuat dari SPT ini
    // Dapat memilih lebih dari satu moda transportasi
    public $transport_mode_ids = [];
    // Origin place now refers to Nota Dinas' origin place via SPT → Nota Dinas
    public $origin_place_id = '';
    // Kota tujuan tidak ditampilkan di form; diisi otomatis dari Nota Dinas
    public $destination_city_id = '';

    // Gunakan tanggal dari Nota Dinas (jika ada) untuk hari
    // Tanggal berangkat/kembali tidak ditampilkan di form; diisi otomatis dari Nota Dinas
    #[Rule('required|integer|min:1')]
    public $days_count = 1;

    // Sumber dana tidak ditampilkan di form (opsional)
    public $funding_source = '';

    // Penandatangan
    #[Rule('required|exists:users,id')]
    public $signed_by_user_id = '';
    #[Rule('nullable|string')]
    public $assignment_title = '';
    public $use_custom_assignment_title = false;


    // Sub Kegiatan
    #[Rule('required|exists:sub_keg,id')]
    public $sub_keg_id = '';

    // Bantuan UI
    public $participants = [];
    public $format_string = null;
    public $format_example = null;

    public function mount($spt_id = null): void
    {
        $this->spt_id = $spt_id ?? request()->query('spt_id');
        if (!$this->spt_id) {
            session()->flash('error', 'SPT tidak ditemukan untuk pembuatan SPPD.');
            $this->redirect(route('spt.index'));
            return;
        }

        $this->spt = Spt::with([
            'notaDinas.participants.user', 
            'notaDinas.destinationCity.province', 
            'notaDinas.requestingUnit',
            'notaDinas.fromUser.position',
            'notaDinas.toUser.position',
            'signedByUser.position',
            'sppds'
        ])->findOrFail($this->spt_id);

        // Prefill nilai umum
        $this->sppd_date = $this->spt->spt_date ?: now()->format('Y-m-d');

        $this->destination_city_id = $this->spt->notaDinas?->destination_city_id ?: '';

        // Get all participants from Nota Dinas for display
        $this->participants = $this->spt->notaDinas?->participants?->map(function ($p) {
            return [
                'id' => $p->user?->id,
                'name' => $p->user?->fullNameWithTitles() ?? $p->user?->name,
                'nip' => $p->user?->nip,
            ];
        })->values()->all();

        // Default penandatangan dari SPT
        $this->signed_by_user_id = $this->spt->signed_by_user_id ?? '';
        // Set assignment title default dari penandatangan jika ada
        $this->assignment_title = $this->guessAssignmentTitle();

        // Check if there are participants available for SPPD creation
        if (empty($this->participants)) {
            session()->flash('error', 'Semua peserta sudah memiliki SPPD. Tidak ada peserta baru yang dapat dibuatkan SPPD.');
            $this->redirect(route('documents'));
            return;
        }

        // Tampilkan format aktif SPPD untuk verifikasi cepat
        $unitScopeId = $this->spt->notaDinas?->requesting_unit_id;
        $fmt = DocNumberFormat::where('doc_type', 'SPPD')
            ->where(function($q) use ($unitScopeId) {
                $q->where('unit_scope_id', $unitScopeId)->orWhereNull('unit_scope_id');
            })
            ->where('is_active', true)
            ->orderByRaw('unit_scope_id is null')
            ->first();
        if ($fmt) {
            $this->format_string = $fmt->format_string;
            $date = $this->sppd_date ? \Carbon\Carbon::parse($this->sppd_date) : now();
            $unitCode = null;
            if ($unitScopeId) {
                $unit = \App\Models\Unit::find($unitScopeId);
                $unitCode = $unit ? ($unit->code ?? $unit->id) : $unitScopeId;
            }
            $replace = [
                '{seq}' => str_pad('1', $fmt->padding, '0', STR_PAD_LEFT),
                '{doc_code}' => $fmt->doc_code,
                '{unit_code}' => $unitCode,
                '{roman_month}' => \App\Services\DocumentNumberService::romanMonth($date->month),
                '{month}' => $date->format('m'),
                '{year}' => $date->format('Y'),
            ];
            $example = $fmt->format_string;
            foreach ($replace as $k => $v) { $example = str_replace($k, $v, $example); }
            $this->format_example = $example;
        }
    }

    public function toggleSelectAll(bool $checked): void
    {
        // No longer needed since 1 SPPD represents all participants
    }

    public function updatedSignedByUserId(): void
    {
        // Jika assignment_title kosong, isi otomatis saat penandatangan berubah
        if (!trim((string)$this->assignment_title)) {
            $this->assignment_title = $this->guessAssignmentTitle();
        }
    }

    public function updatedUseCustomAssignmentTitle($val): void
    {
        // Jika custom assignment title dimatikan, reset ke default
        if (!$val) {
            $this->assignment_title = $this->guessAssignmentTitle();
        }
    }

    private function guessAssignmentTitle(): string
    {
        if (!$this->signed_by_user_id) return '';
        $u = User::with(['position'])->find($this->signed_by_user_id);
        if (!$u) return '';
        return (string)($u->position_desc ?: ($u->position->name ?? ''));
    }

    public function save(): mixed
    {
        $this->validate([
            'sppd_date' => 'required|date',
            'transport_mode_ids' => 'required|array|min:1',
            'transport_mode_ids.*' => 'exists:transport_modes,id',
            'signed_by_user_id' => 'required|exists:users,id',
            'assignment_title' => 'nullable|string',
            'sub_keg_id' => 'required|exists:sub_keg,id',
        ]);

        if (!$this->spt) {
            session()->flash('error', 'SPT tidak ditemukan.');
            return null;
        }

        $unitScopeId = $this->spt->notaDinas?->requesting_unit_id;
        if (!$unitScopeId) {
            session()->flash('error', 'Unit pada Nota Dinas tidak ditemukan. Pastikan field unit terisi.');
            return null;
        }

        // Wajib: tujuan harus ada dari ND (tidak ditampilkan di form)
        if (!$this->destination_city_id) {
            $this->destination_city_id = $this->spt->notaDinas?->destination_city_id;
        }
        if (!$this->destination_city_id) {
            session()->flash('error', 'Kota tujuan pada Nota Dinas tidak ditemukan.');
            return null;
        }

        DB::beginTransaction();
        try {
            $gen = DocumentNumberService::generate('SPPD', $unitScopeId, $this->sppd_date ?: now(), [
                'spt_id' => $this->spt->id,
            ], Auth::id());

            // Atur assignment title berdasarkan mode
            $assignmentTitle = trim((string)$this->assignment_title);
            if (!$this->use_custom_assignment_title || $assignmentTitle === '') {
                $assignmentTitle = $this->guessAssignmentTitle();
            }

            $sppd = Sppd::create([
                'doc_no' => $gen['number'],
                'number_is_manual' => false,
                'number_manual_reason' => null,
                'number_format_id' => isset($gen['format']) ? $gen['format']->id : null,
                'number_sequence_id' => isset($gen['sequence']) ? $gen['sequence']->id : null,
                'number_scope_unit_id' => $unitScopeId,
                'sppd_date' => $this->sppd_date,
                'spt_id' => $this->spt->id,
                'signed_by_user_id' => $this->signed_by_user_id,
                'sub_keg_id' => $this->sub_keg_id,
                'assignment_title' => $assignmentTitle,
                'funding_source' => $this->funding_source,
            ]);

            // Attach transport modes
            if (is_array($this->transport_mode_ids) && count($this->transport_mode_ids) > 0) {
                $sppd->transportModes()->attach($this->transport_mode_ids);
            }

            // Create snapshot of signed_by_user data
            $sppd->createSignedByUserSnapshot();

            DB::commit();
            session()->flash('message', 'SPPD berhasil dibuat untuk semua peserta.');
            // Redirect ke halaman utama dengan state yang sama
            return $this->redirect(route('documents', [
                'nota_dinas_id' => $this->spt->nota_dinas_id,
                'spt_id' => $this->spt->id
            ]));

        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal membuat SPPD: ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        // Get sub kegiatan with filtering based on user role
        $subKegiatanQuery = SubKeg::with(['unit', 'activeRekeningBelanja'])->orderBy('kode_subkeg');
        
        // Apply unit scope filtering for bendahara pengeluaran pembantu
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId) {
                $subKegiatanQuery->where('id_unit', $userUnitId);
            }
        }
        
        return view('livewire.sppd.create', [
            'transportModes' => TransportMode::orderBy('name')->get(),
            'orgPlaces' => OrgPlace::orderBy('name')->get(),
            'subKegiatan' => $subKegiatanQuery->get(),
        ]);
    }
}

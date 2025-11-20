<?php

namespace App\Livewire\Spt;

use App\Models\Spt;
use App\Models\NotaDinas;
use App\Models\User;
use App\Models\Unit;
use App\Models\DocNumberFormat;
use App\Services\DocumentNumberService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public $nota_dinas_id = null;
    public $notaDinas = null;

    #[Rule('required|exists:units,id')]
    public $requesting_unit_id = '';
    #[Rule('required|exists:users,id')]
    public $to_user_id = '';
    #[Rule('required|exists:users,id')]
    public $from_user_id = '';
    #[Rule('required|date')]
    public $spt_date = '';
    #[Rule('required|string|max:255')]
    public $hal = '';
    #[Rule('required|string')]
    public $dasar = '';
    #[Rule('required|string')]
    public $maksud = '';
    #[Rule('required|array|min:1')]
    public $members = [];
    // Status dihapus dari SPT
    public $status = null;
    public $notes = '';
    public $number_is_manual = false;
    public $number_manual_reason = '';
    public $manual_doc_no = '';

    // Penandatangan
    #[Rule('required|exists:users,id')]
    public $signed_by_user_id = '';
    #[Rule('nullable|string')]
    public $assignment_title = '';
    public $use_custom_assignment_title = false;

    // Bantuan format penomoran manual
    public $format_string = null;
    public $format_example = null;

    public function mount($nota_dinas_id = null)
    {
        $this->nota_dinas_id = $nota_dinas_id ?? request()->query('nota_dinas_id');
        if (!$this->nota_dinas_id) {
            session()->flash('error', 'Nota Dinas tidak ditemukan untuk pembuatan SPT.');
            $this->redirect(route('nota-dinas.index'));
            return;
        }
        
        if ($this->nota_dinas_id) {
            $this->notaDinas = NotaDinas::with(['participants.user', 'requestingUnit', 'toUser', 'fromUser'])->findOrFail($this->nota_dinas_id);
            // Ambil semua dari ND (tidak ditampilkan di form)
            $this->requesting_unit_id = $this->notaDinas->requesting_unit_id;
            $this->to_user_id = $this->notaDinas->to_user_id;
            $this->from_user_id = $this->notaDinas->from_user_id;
            $this->hal = $this->notaDinas->hal;
            $this->dasar = $this->notaDinas->dasar;
            $this->maksud = $this->notaDinas->maksud;
            $this->spt_date = now()->format('Y-m-d');
            // Peserta bawaan ND
            $this->members = $this->notaDinas->participants->pluck('user_id')->toArray();
            // Default penandatangan dari ND
            $this->signed_by_user_id = $this->notaDinas->to_user_id ?? '';
            // Set assignment title default dari penandatangan jika ada
            $this->assignment_title = $this->guessAssignmentTitle();
        }

        // Ambil format aktif SPT (scope global)
        $fmt = DocNumberFormat::where('doc_type', 'SPT')
            ->whereNull('unit_scope_id')
            ->where('is_active', true)
            ->first();
        if ($fmt) {
            $this->format_string = $fmt->format_string;
            // Bangun contoh sederhana (seq 001, tanggal spt atau hari ini)
            $date = $this->spt_date ? \Carbon\Carbon::parse($this->spt_date) : now();
            $replace = [
                '{seq}' => str_pad('1', $fmt->padding, '0', STR_PAD_LEFT),
                '{doc_code}' => $fmt->doc_code,
                '{unit_code}' => '',
                '{roman_month}' => $this->romanMonth((int)$date->format('m')),
                '{month}' => $date->format('m'),
                '{year}' => $date->format('Y'),
            ];
            $example = $fmt->format_string;
            foreach ($replace as $k => $v) { $example = str_replace($k, $v, $example); }
            $this->format_example = $example;
        }
    }

    public function updatedSignedByUserId(): void
    {
        // Jika assignment_title kosong, isi otomatis saat penandatangan berubah
        if (!trim((string)$this->assignment_title)) {
            $this->assignment_title = $this->guessAssignmentTitle();
        }
    }

    public function updatedNumberIsManual($val): void
    {
        // Reset nilai manual saat toggle berubah untuk menghindari kebingungan
        if (!$val) {
            $this->manual_doc_no = '';
            $this->number_manual_reason = '';
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

    protected function romanMonth(int $m): string
    {
        $romans = [null,'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        return $romans[$m] ?? (string)$m;
    }

    public function save()
    {
        $this->validate([
            'spt_date' => 'required|date',
            'number_is_manual' => 'boolean',
            'manual_doc_no' => 'nullable|required_if:number_is_manual,true|string',
            'number_manual_reason' => 'nullable|required_if:number_is_manual,true|string',
            'signed_by_user_id' => 'required|exists:users,id',
            'assignment_title' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        try {
            // Generate nomor dokumen
            $doc_no = null;
            $number_is_manual = false;
            $number_manual_reason = null;
            $format = null; $sequence = null;
            
            if ($this->number_is_manual && $this->manual_doc_no) {
                $doc_no = $this->manual_doc_no;
                $number_is_manual = true;
                $number_manual_reason = $this->number_manual_reason;
                // Audit override
                DocumentNumberService::override('SPT', null, $doc_no, $number_manual_reason, auth()->id(), [
                    'nota_dinas_id' => $this->nota_dinas_id,
                ]);
            } else {
                // Generate otomatis (scope global / bukan per unit)
                $gen = DocumentNumberService::generate('SPT', null, $this->spt_date ?: now(), [
                    'nota_dinas_id' => $this->nota_dinas_id,
                ], auth()->id());
                $doc_no = $gen['number'];
                $format = $gen['format'];
                $sequence = $gen['sequence'];
            }

            // Create SPT
            $assignmentTitle = trim((string)$this->assignment_title);
            if ($assignmentTitle === '') {
                $assignmentTitle = $this->guessAssignmentTitle();
            }

            $spt = Spt::create([
                'doc_no' => $doc_no,
                'number_is_manual' => $number_is_manual,
                'number_manual_reason' => $number_manual_reason,
                'number_format_id' => $format->id ?? null,
                'number_sequence_id' => $sequence->id ?? null,
                'number_scope_unit_id' => null,
                'nota_dinas_id' => $this->nota_dinas_id,
                'spt_date' => $this->spt_date,
                'signed_by_user_id' => $this->signed_by_user_id,
                'assignment_title' => $assignmentTitle,
                // status dihapus
                'notes' => $this->notes,
            ]);

            // Create snapshot of signed_by_user data
            $spt->createSignedByUserSnapshot();

            // Tidak lagi membuat SPT members; peserta akan selalu dirujuk dari Nota Dinas

            DB::commit();
            
            session()->flash('message', 'SPT berhasil dibuat.');
            // Redirect ke halaman utama dengan state yang sama
            return $this->redirect(route('documents', [
                'nota_dinas_id' => $this->nota_dinas_id,
                'spt_id' => $spt->id
            ]));
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal membuat SPT: ' . $e->getMessage());
            throw $e;
        }
    }

    public function render()
    {
        $units = Unit::orderBy('name')->get();
        $signers = User::where('is_signer', true)->orderBy('name')->get();
        
        return view('livewire.spt.create', [
            'units' => $units,
            'users' => collect(),
            'nota_dinas_id' => $this->nota_dinas_id,
            'notaDinas' => $this->notaDinas,
            'signers' => $signers,
        ]);
    }
}

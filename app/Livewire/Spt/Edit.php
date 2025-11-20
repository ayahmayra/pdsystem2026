<?php

namespace App\Livewire\Spt;

use App\Models\Spt;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public Spt $spt;

    #[Rule('required|date')]
    public $spt_date = '';
    #[Rule('required|exists:users,id')]
    public $signed_by_user_id = '';
    #[Rule('nullable|string')]
    public $assignment_title = '';
    public $use_custom_assignment_title = false;
    #[Rule('boolean')]
    public $number_is_manual = false;
    #[Rule('nullable|string')]
    public $number_manual_reason = '';
    #[Rule('nullable|string')]
    public $manual_doc_no = '';
    // Status dihapus dari SPT; gunakan catatan saja jika perlu
    public $status = null;
    public $notes = '';

    private function guessAssignmentTitle(): string
    {
        if (!$this->signed_by_user_id) return '';
        $u = User::with(['position'])->find($this->signed_by_user_id);
        if (!$u) return '';
        return (string)($u->position_desc ?: ($u->position->name ?? ''));
    }

    public function mount(Spt $spt)
    {
        $this->spt = $spt->load(['notaDinas.requestingUnit', 'notaDinas.fromUser.position', 'notaDinas.toUser.position', 'notaDinas.destinationCity.province', 'notaDinas.participants.user']);
        $this->spt_date = $spt->spt_date ? (is_string($spt->spt_date) ? $spt->spt_date : \Carbon\Carbon::parse($spt->spt_date)->format('Y-m-d')) : '';
        $this->signed_by_user_id = $spt->signed_by_user_id;
        $this->assignment_title = $spt->assignment_title;
        
        // Set custom assignment title berdasarkan apakah assignment_title berbeda dari default
        $defaultTitle = $this->guessAssignmentTitle();
        $this->use_custom_assignment_title = !empty(trim($spt->assignment_title)) && trim($spt->assignment_title) !== trim($defaultTitle);
        
        $this->number_is_manual = (bool)$spt->number_is_manual;
        $this->number_manual_reason = $spt->number_manual_reason;
        $this->manual_doc_no = $spt->number_is_manual ? $spt->doc_no : '';
        $this->status = null;
        $this->notes = $spt->notes;
    }

    public function save()
    {
        $this->validate([
            'spt_date' => 'required|date',
            'signed_by_user_id' => 'required|exists:users,id',
            'assignment_title' => 'nullable|string',
            'number_is_manual' => 'boolean',
            'manual_doc_no' => 'nullable|required_if:number_is_manual,true|string',
            'number_manual_reason' => 'nullable|required_if:number_is_manual,true|string',
            // status dihapus
        ]);

        DB::beginTransaction();
        try {
            // Atur nomor jika manual
            $doc_no = $this->spt->doc_no;
            $number_is_manual = $this->number_is_manual;
            $number_manual_reason = $this->number_manual_reason;
            if ($this->number_is_manual && $this->manual_doc_no) {
                $doc_no = $this->manual_doc_no;
            }

            // Atur assignment title berdasarkan mode
            $assignmentTitle = trim((string)$this->assignment_title);
            if (!$this->use_custom_assignment_title || $assignmentTitle === '') {
                $assignmentTitle = $this->guessAssignmentTitle();
            }

            $this->spt->update([
                'doc_no' => $doc_no,
                'number_is_manual' => $number_is_manual,
                'number_manual_reason' => $number_manual_reason,
                'spt_date' => $this->spt_date,
                'signed_by_user_id' => $this->signed_by_user_id,
                'assignment_title' => $assignmentTitle,
                // status dihapus
                'notes' => $this->notes,
            ]);

            // Refresh model to get updated relationships
            $this->spt->refresh();

            // Update snapshot of signed_by_user data only if signatory changed or snapshot doesn't exist
            if ($this->spt->signed_by_user_id !== $this->signed_by_user_id || !$this->spt->signed_by_user_name_snapshot) {
                $this->spt->createSignedByUserSnapshot();
            }

            DB::commit();
            session()->flash('message', 'SPT berhasil diperbarui.');
            // Redirect ke halaman utama dengan state yang sama
            return $this->redirect(route('documents', [
                'nota_dinas_id' => $this->spt->nota_dinas_id,
                'spt_id' => $this->spt->id
            ]));
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memperbarui SPT: ' . $e->getMessage());
        }
    }

    public function updatedUseCustomAssignmentTitle($val): void
    {
        // Jika custom assignment title dimatikan, reset ke default
        if (!$val) {
            $this->assignment_title = $this->guessAssignmentTitle();
        }
    }

    public function updatedSignedByUserId(): void
    {
        // Jika assignment_title kosong atau tidak custom, isi otomatis saat penandatangan berubah
        if (!$this->use_custom_assignment_title || !trim((string)$this->assignment_title)) {
            $this->assignment_title = $this->guessAssignmentTitle();
        }
    }

    public function render()
    {
        $signers = User::where('is_signer', true)->orderBy('name')->get();
        return view('livewire.spt.edit', [
            'signers' => $signers,
        ]);
    }
}

<?php

namespace App\Livewire\Sppd;

use App\Models\Sppd;
use App\Models\User;
use App\Models\OrgPlace;
use App\Models\City;
use App\Models\TransportMode;
use App\Models\Spt;
use App\Models\SubKeg;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public $sppd_id = null;
    public $sppd = null;

    #[Rule('required|date')]
    public $sppd_date = '';



    #[Rule('required|array|min:1')]
    public $transport_mode_ids = [];







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

    public function mount($sppd_id = null): void
    {
        $this->sppd_id = $sppd_id ?? request()->route('sppd');
        if (!$this->sppd_id) {
            session()->flash('error', 'SPPD tidak ditemukan.');
            $this->redirect(route('sppd.index'));
            return;
        }

        $this->sppd = Sppd::with([
            'spt.notaDinas.participants.user', 
            'spt.notaDinas.originPlace',
            'spt.notaDinas.destinationCity.province',
            'spt.notaDinas.requestingUnit',
            'spt.notaDinas.fromUser.position',
            'spt.notaDinas.toUser.position',
            'spt.signedByUser.position',
            'transportModes'
        ])->findOrFail($this->sppd_id);

        // Load data SPPD ke form
        $this->sppd_date = $this->sppd->sppd_date ?: now()->format('Y-m-d');
        $this->transport_mode_ids = $this->sppd->transportModes->pluck('id')->toArray();


        $this->funding_source = $this->sppd->funding_source ?: '';

        // Load penandatangan dan assignment title
        $this->signed_by_user_id = $this->sppd->signed_by_user_id ?? '';
        $this->assignment_title = $this->sppd->assignment_title ?? '';
        
        
        // Load Sub Kegiatan
        $this->sub_keg_id = $this->sppd->sub_keg_id ?? '';
        
        // Set custom assignment title berdasarkan apakah assignment_title berbeda dari default
        $defaultTitle = $this->guessAssignmentTitle();
        $this->use_custom_assignment_title = !empty(trim($this->sppd->assignment_title)) && trim($this->sppd->assignment_title) !== trim($defaultTitle);


    }

    public function updatedSignedByUserId(): void
    {
        // Jika assignment_title kosong atau tidak custom, isi otomatis saat penandatangan berubah
        if (!$this->use_custom_assignment_title || !trim((string)$this->assignment_title)) {
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

        if (!$this->sppd) {
            session()->flash('error', 'SPPD tidak ditemukan.');
            return null;
        }

        try {
            // Simpan nilai awal sebelum update
            $originalSignedByUserId = $this->sppd->signed_by_user_id;
            
            // Atur assignment title berdasarkan mode
            $assignmentTitle = trim((string)$this->assignment_title);
            if (!$this->use_custom_assignment_title || $assignmentTitle === '') {
                $assignmentTitle = $this->guessAssignmentTitle();
            }

            $this->sppd->update([
                'sppd_date' => $this->sppd_date,
                'funding_source' => $this->funding_source,
                'signed_by_user_id' => $this->signed_by_user_id,
                'sub_keg_id' => $this->sub_keg_id,
                'assignment_title' => $assignmentTitle,
            ]);

            // Refresh model to get updated relationships
            $this->sppd->refresh();

            // Sync transport modes
            $this->sppd->transportModes()->sync($this->transport_mode_ids);

            // Update snapshot of signed_by_user data only if signatory changed
            if ($originalSignedByUserId != $this->signed_by_user_id || !$this->sppd->signed_by_user_name_snapshot) {
                $this->sppd->createSignedByUserSnapshot();
            }

            session()->flash('message', 'SPPD berhasil diperbarui.');
            // Redirect ke halaman utama dengan state yang sama
            return $this->redirect(route('documents', [
                'nota_dinas_id' => $this->sppd->spt->nota_dinas_id,
                'spt_id' => $this->sppd->spt_id,
                'sppd_id' => $this->sppd->id
            ]));
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui SPPD: ' . $e->getMessage());
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
        
        return view('livewire.sppd.edit', [
            'transportModes' => TransportMode::orderBy('name')->get(),
            'orgPlaces' => OrgPlace::orderBy('name')->get(),
            'cities' => City::orderBy('name')->get(),
            'subKegiatan' => $subKegiatanQuery->get(),
        ]);
    }
}

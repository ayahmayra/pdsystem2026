<?php

namespace App\Livewire\TripReports;

use App\Models\TripReport;
use App\Models\Spt;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Rule;

class Create extends Component
{
    #[Rule('required|exists:spt,id')]
    public $spt_id = '';

    #[Rule('required|string')]
    public $activities = '';

    #[Rule('required|date')]
    public $report_date = '';

    public $report_no = '';

    public function mount()
    {
        $this->report_date = date('Y-m-d');
        
        // Require spt_id via query parameter coming from Documents page
        $sptId = request()->query('spt_id');
        if ($sptId) {
            $this->spt_id = $sptId;
        } else {
            session()->flash('error', 'Pilih SPT dari halaman Dokumen terlebih dahulu');
            $this->redirect(route('documents'));
        }
    }

    public function save()
    {
        $this->validate();

        // Ensure SPT exists
        $spt = Spt::with(['notaDinas.originPlace', 'notaDinas.destinationCity'])->findOrFail($this->spt_id);

        // Prevent duplicate report for the same SPT
        $existingReport = TripReport::where('spt_id', $this->spt_id)->first();
        if ($existingReport) {
            session()->flash('error', 'Laporan perjalanan dinas untuk SPT ini sudah ada.');
            return;
        }

        // Laporan perjalanan dinas tidak menggunakan penomoran otomatis
        // $docNumberResult = DocumentNumberService::generate('LAP', Auth::user()->unit_id);

        // Auto-fill fields from Nota Dinas
        $placeFrom = $spt->notaDinas?->originPlace?->name ?? '';
        $placeTo = $spt->notaDinas?->destinationCity?->name ?? '';
        $departDate = $spt->notaDinas?->start_date ?? $spt->start_date ?? null;
        $returnDate = $spt->notaDinas?->end_date ?? $spt->end_date ?? null;

        TripReport::create([
            'doc_no' => null, // Laporan tidak menggunakan penomoran otomatis
            'number_is_manual' => false,
            'number_manual_reason' => null,
            'number_format_id' => null,
            'number_sequence_id' => null,
            'number_scope_unit_id' => Auth::user()->unit_id,
            'spt_id' => $this->spt_id,
            'report_no' => $this->report_no ?: null,
            'report_date' => $this->report_date,
            'place_from' => $placeFrom,
            'place_to' => $placeTo,
            'depart_date' => $departDate,
            'return_date' => $returnDate,
            'activities' => $this->activities,
            'created_by_user_id' => Auth::id(),
        ]);

        session()->flash('message', 'Laporan perjalanan dinas berhasil dibuat.');

        // Redirect back to documents page with selected state
        $spt = Spt::with(['notaDinas'])->findOrFail($this->spt_id);
        $notaDinasId = $spt->nota_dinas_id;
        $sptId = $this->spt_id;
        
        // Get the first SPPD for this SPT to maintain complete state
        $firstSppd = $spt->sppds()->first();
        $sppdId = $firstSppd ? $firstSppd->id : null;
        
        // Ensure we redirect to show the newly created report
        $redirectParams = [
            'nota_dinas_id' => $notaDinasId,
            'spt_id' => $sptId
        ];
        
        if ($sppdId) {
            $redirectParams['sppd_id'] = $sppdId;
        }
        
        // Add a flag to indicate successful creation
        $redirectParams['report_created'] = 'true';
        
        return redirect()->route('documents', $redirectParams);
    }

    public function getBackUrl()
    {
        if ($this->spt_id) {
            $spt = Spt::with(['notaDinas'])->find($this->spt_id);
            if ($spt) {
                $notaDinasId = $spt->nota_dinas_id;
                $sptId = $this->spt_id;
                
                // Get the first SPPD for this SPT to maintain complete state
                $firstSppd = $spt->sppds()->first();
                $sppdId = $firstSppd ? $firstSppd->id : null;
                
                $redirectParams = [
                    'nota_dinas_id' => $notaDinasId,
                    'spt_id' => $sptId
                ];
                
                if ($sppdId) {
                    $redirectParams['sppd_id'] = $sppdId;
                }
                
                return route('documents', $redirectParams);
            }
        }
        
        return route('documents');
    }

    public function render()
    {
        // For create, we don't list SPTs here. Display selected SPT info only.
        $spt = $this->spt_id ? Spt::with(['notaDinas.originPlace', 'notaDinas.destinationCity'])->find($this->spt_id) : null;

        return view('livewire.trip-reports.create', [
            'spt' => $spt,
        ]);
    }
}

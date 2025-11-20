<?php

namespace App\Livewire\TripReports;

use App\Models\TripReport;
use App\Models\Spt;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Rule;

class Edit extends Component
{
    public TripReport $tripReport;



    #[Rule('required|string')]
    public $activities = '';

    #[Rule('required|date')]
    public $report_date = '';

    public $report_no = '';

    public function mount(TripReport $tripReport)
    {
        // Load the trip report with all necessary relationships
        $this->tripReport = $tripReport->load(['spt.sppds', 'spt.notaDinas.originPlace', 'spt.notaDinas.destinationCity']);
        $this->activities = $tripReport->activities;
        $this->report_date = $tripReport->report_date;
        $this->report_no = $tripReport->report_no;
    }

    public function update()
    {
        $this->validate();

        $this->tripReport->update([
            'activities' => $this->activities,
            'report_date' => $this->report_date,
            'report_no' => $this->report_no,
        ]);

        session()->flash('message', 'Laporan perjalanan dinas berhasil diperbarui.');

        // Redirect back to documents page with selected state
        $notaDinasId = $this->tripReport->spt->nota_dinas_id;
        $sptId = $this->tripReport->spt_id;
        
        // Get the first SPPD for this SPT to maintain complete state
        $firstSppd = $this->tripReport->spt->sppds()->first();
        $sppdId = $firstSppd ? $firstSppd->id : null;
        
        // Ensure we redirect to show the updated report
        $redirectParams = [
            'nota_dinas_id' => $notaDinasId,
            'spt_id' => $sptId
        ];
        
        if ($sppdId) {
            $redirectParams['sppd_id'] = $sppdId;
        }
        
        // Add a flag to indicate successful update
        $redirectParams['report_updated'] = 'true';
        
        return redirect()->route('documents', $redirectParams);
    }

    public function getBackUrl()
    {
        $notaDinasId = $this->tripReport->spt->nota_dinas_id;
        $sptId = $this->tripReport->spt_id;
        
        // Get the first SPPD for this SPT to maintain complete state
        $firstSppd = $this->tripReport->spt->sppds->first();
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

    public function render()
    {
        // Eager load SPT with Nota Dinas data
        $this->tripReport->load(['spt.notaDinas.originPlace', 'spt.notaDinas.destinationCity']);
        
        return view('livewire.trip-reports.edit');
    }
}

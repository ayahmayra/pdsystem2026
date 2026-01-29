<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\NotaDinas;
use App\Models\Spt;
use App\Models\Sppd;
use App\Helpers\PermissionHelper;


class MainPage extends Component
{
    public $selectedNotaDinasId = null;
    public $selectedSptId = null;
    public $selectedSppdId = null;
    
    public $selectedNotaDinas = null;
    public $selectedSpt = null;
    public $selectedSppd = null;

    // Computed properties for child components
    public function getCurrentNotaDinas()
    {
        if ($this->selectedNotaDinasId) {
            return NotaDinas::with(['spt.sppds'])->find($this->selectedNotaDinasId);
        }
        return null;
    }

    public function getCurrentSpt()
    {
        if ($this->selectedSptId) {
            return Spt::with(['sppds'])->find($this->selectedSptId);
        }
        return null;
    }

    #[On('ndSelected')]
    public function handleNotaDinasSelected($notaDinasId)
    {
        $this->selectedNotaDinasId = $notaDinasId;
        $this->selectedSptId = null;
        $this->selectedSppdId = null;
        
        // Load the actual Nota Dinas data
        $this->selectedNotaDinas = NotaDinas::with(['spt.sppds', 'spt.notaDinas.originPlace', 'spt.notaDinas.destinationCity'])->find($notaDinasId);
        
        // Dispatch specific events to child components
    }

    #[On('spt-selected')]
    public function handleSptSelected($sptId)
    {
        $this->selectedSptId = $sptId;
        $this->selectedSppdId = null;
        
        // Load the actual SPT data
        $this->selectedSpt = Spt::with(['sppds'])->find($sptId);
    }

    #[On('sppd-selected')]
    public function handleSppdSelected($sppdId)
    {
        $this->selectedSppdId = $sppdId;
        
        // Load the actual SPPD data
        $this->selectedSppd = Sppd::with(['spt.notaDinas.participants', 'signedByUser', 'receipts'])->find($sppdId);
    }


    #[On('refreshAll')]
    public function refreshData()
    {
        // Refresh selected data with proper eager loading
        if ($this->selectedNotaDinasId) {
            $this->selectedNotaDinas = NotaDinas::with(['spt.sppds', 'spt.notaDinas.originPlace', 'spt.notaDinas.destinationCity'])->find($this->selectedNotaDinasId);
        }
        if ($this->selectedSptId) {
            $this->selectedSpt = Spt::with(['sppds', 'notaDinas.originPlace', 'notaDinas.destinationCity'])->find($this->selectedSptId);
        }
        if ($this->selectedSppdId) {
            $this->selectedSppd = Sppd::with(['spt.notaDinas.originPlace', 'spt.notaDinas.destinationCity'])->find($this->selectedSppdId);
        }
        
        // Force refresh of trip report data to ensure latest data is displayed
        if ($this->selectedSptId) {
            // This will trigger a re-render of the trip report section
            $this->dispatch('trip-report-refreshed');
        }
    }

    public function mount()
    {
        // Check if user can view documents
        if (!PermissionHelper::can('documents.view')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat dokumen.');
        }

        // Check for query parameters first
        $notaDinasId = request()->query('nota_dinas_id');
        $sptId = request()->query('spt_id');
        $sppdId = request()->query('sppd_id');
        $reportCreated = request()->query('report_created');
        $reportUpdated = request()->query('report_updated');
        
        if ($notaDinasId) {
            $this->selectedNotaDinasId = $notaDinasId;
            // Load the actual Nota Dinas data
            $this->selectedNotaDinas = NotaDinas::with(['spt.sppds'])->find($notaDinasId);
            $this->dispatch('loadSpts', $notaDinasId);
            
            if ($sptId) {
                $this->selectedSptId = $sptId;
                // Load the actual SPT data
                $this->selectedSpt = Spt::with(['sppds'])->find($sptId);
                $this->dispatch('loadSppds', $sptId);
                
                if ($sppdId) {
                    $this->selectedSppdId = $sppdId;
                    // Load the actual SPPD data
                    $this->selectedSppd = Sppd::find($sppdId);
                }
            }
            
            // If report was created or updated, ensure we show success message and refresh data
            if ($reportCreated) {
                session()->flash('message', 'Laporan perjalanan dinas berhasil dibuat dan ditampilkan.');
                $this->refreshData();
            } elseif ($reportUpdated) {
                session()->flash('message', 'Laporan perjalanan dinas berhasil diperbarui dan ditampilkan.');
                $this->refreshData();
            }
        } else {
            // Initialize with latest Nota Dinas if available (with unit scope filtering)
            $query = NotaDinas::with(['spt.sppds'])->latest('created_at');
            
            // Apply unit scope filtering for bendahara pengeluaran pembantu
            if (!PermissionHelper::canAccessAllData()) {
                $userUnitId = PermissionHelper::getUserUnitId();
                if ($userUnitId) {
                    $query->where('requesting_unit_id', $userUnitId);
                }
            }
            
            $latestNd = $query->first();
            if ($latestNd) {
                $this->selectedNotaDinasId = $latestNd->id;
                $this->selectedNotaDinas = $latestNd;
                // Dispatch events to load child components
                $this->dispatch('loadSpts', $latestNd->id);
                if ($latestNd->spt) {
                    $this->selectedSptId = $latestNd->spt->id;
                    $this->selectedSpt = $latestNd->spt;
                    $this->dispatch('loadSppds', $latestNd->spt->id);
                }
            }
        }
    }





    public function createLaporanPd()
    {
        // Use the selected SPT ID to avoid null object issues across requests
        if (!$this->selectedSptId) {
            session()->flash('error', 'Pilih SPT terlebih dahulu');
            return;
        }
        
        // Check if trip report already exists
        $existingReport = \App\Models\TripReport::where('spt_id', $this->selectedSptId)->first();
        if ($existingReport) {
            session()->flash('error', 'Laporan perjalanan dinas untuk SPT ini sudah ada');
            return;
        }
        
        // Redirect to create trip report with SPT ID as query parameter
        return $this->redirect(route('trip-reports.create') . '?spt_id=' . $this->selectedSptId);
    }

    public function createKwitansi()
    {
        // Use the selected SPPD ID to avoid null object issues across requests
        if (!$this->selectedSppdId) {
            session()->flash('error', 'Pilih SPPD terlebih dahulu');
            return;
        }
        
        // Check if the selected SPPD has participants without receipts
        $sppd = \App\Models\Sppd::with(['spt.notaDinas.participants.user'])->find($this->selectedSppdId);
        if (!$sppd) {
            session()->flash('error', 'SPPD tidak ditemukan');
            return;
        }
        
        // Check if there are participants without receipts
        $allParticipants = $sppd->spt->notaDinas->participants;
        $participantsWithReceipts = \App\Models\Receipt::where('sppd_id', $this->selectedSppdId)
            ->pluck('payee_user_id')
            ->toArray();
        
        $availableParticipants = $allParticipants->filter(function ($participant) use ($participantsWithReceipts) {
            return !in_array($participant->user_id, $participantsWithReceipts);
        });
        
        if ($availableParticipants->isEmpty()) {
            session()->flash('error', 'SPPD ini sudah memiliki kwitansi untuk semua peserta');
            return;
        }
        
        // Redirect to create receipt page with SPPD ID
        return $this->redirect(route('receipts.create') . '?sppd_id=' . $this->selectedSppdId);
    }

    public function deleteTripReport($tripReportId)
    {
        try {
            $tripReport = \App\Models\TripReport::findOrFail($tripReportId);
            
            // Store current state before deletion
            $notaDinasId = $tripReport->spt->nota_dinas_id;
            $sptId = $tripReport->spt_id;
            $firstSppd = $tripReport->spt->sppds()->first();
            $sppdId = $firstSppd ? $firstSppd->id : null;
            
            // Supporting documents now relate to Nota Dinas, not Trip Report
            // No need to delete supporting documents when deleting trip report
            
            // Delete the trip report
            $tripReport->delete();
            
            session()->flash('message', 'Laporan perjalanan dinas berhasil dihapus.');
            
            // Refresh the data to update the UI
            $this->refreshData();
            
            // Dispatch event to refresh all child components
            $this->dispatch('refreshAll');
            
            // Maintain the current state after deletion
            $this->selectedNotaDinasId = $notaDinasId;
            $this->selectedSptId = $sptId;
            if ($sppdId) {
                $this->selectedSppdId = $sppdId;
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus laporan perjalanan dinas: ' . $e->getMessage());
        }
    }

    public function deleteSupportingDocument($documentId)
    {
        try {
            $document = \App\Models\SupportingDocument::findOrFail($documentId);
            
            // Store document info for confirmation message
            $documentTitle = $document->title;
            
            // Delete the document
            $document->delete();
            
            session()->flash('message', "Dokumen '{$documentTitle}' berhasil dihapus.");
            
            // Refresh the data to update the UI
            $this->refreshData();
            
            // Dispatch event to refresh all child components
            $this->dispatch('refreshAll');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus dokumen pendukung: ' . $e->getMessage());
        }
    }

    public function deleteReceipt($receiptId)
    {
        try {
            $receipt = \App\Models\Receipt::findOrFail($receiptId);
            
            // Store current state before deletion
            $sppdId = $receipt->sppd_id;
            $sptId = $receipt->sppd->spt_id;
            $notaDinasId = $receipt->sppd->spt->nota_dinas_id;
            
            // Store receipt info for confirmation message
            $receiptNo = $receipt->receipt_no ?? 'Kwitansi Manual';
            
            // Delete the receipt
            $receipt->delete();
            
            session()->flash('message', "Kwitansi '{$receiptNo}' berhasil dihapus.");
            
            // Refresh the data to update the UI
            $this->refreshData();
            
            // Dispatch event to refresh all child components
            $this->dispatch('refreshAll');
            
            // Maintain the current state after deletion
            $this->selectedNotaDinasId = $notaDinasId;
            $this->selectedSptId = $sptId;
            $this->selectedSppdId = $sppdId;
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus kwitansi: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.documents.main-page');
    }
}

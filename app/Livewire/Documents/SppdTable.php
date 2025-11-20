<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use App\Models\Sppd;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;

#[Lazy]
class SppdTable extends Component
{
    public $sptId;

    public $selectedSppdId;

    public function mount($sptId = null)
    {
        $this->sptId = $sptId;
    }

    public function selectSppd($sppdId)
    {
        $this->selectedSppdId = $sppdId;
        $this->dispatch('sppd-selected', sppdId: $sppdId);
    }

    public function createSppd($sptId)
    {
        return $this->redirect(route('sppd.create', ['spt_id' => $sptId]));
    }

    #[On('refreshAll')]
    public function refreshData()
    {
        // This will trigger a re-render of the component
    }

    public function confirmDelete($sppdId)
    {
        try {
            $sppd = Sppd::with(['itineraries', 'receipts'])->findOrFail($sppdId);
            
            // Check if SPPD has related data
            if ($sppd->itineraries && $sppd->itineraries->count() > 0) {
                session()->flash('error', 'SPPD tidak dapat dihapus karena memiliki data rute perjalanan.');
                return;
            }
            
            if ($sppd->receipts && $sppd->receipts->count() > 0) {
                session()->flash('error', 'SPPD tidak dapat dihapus karena memiliki data kwitansi.');
                return;
            }
            
            // Store current state before deletion
            $sptId = $sppd->spt_id;
            $notaDinasId = $sppd->spt->nota_dinas_id;
            
            // Delete the SPPD
            $sppd->delete();
            
            session()->flash('message', 'SPPD berhasil dihapus.');
            
            // Dispatch event to refresh parent component
            $this->dispatch('refreshAll');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus SPPD: ' . $e->getMessage());
        }
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="animate-pulse">
            <div class="bg-gray-50 dark:bg-gray-800 px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex space-x-4">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded flex-1"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded flex-1"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded flex-1"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded flex-1"></div>
                </div>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="px-6 py-4">
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                        </div>
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                        </div>
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                        </div>
                        <div class="flex-1">
                            <div class="flex space-x-2">
                                <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                                <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                        </div>
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                        </div>
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                        </div>
                        <div class="flex-1">
                            <div class="flex space-x-2">
                                <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                                <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        HTML;
    }

    public function render()
    {
        $sppds = $this->sptId 
            ? Sppd::where('spt_id', $this->sptId)
                ->with(['spt.notaDinas.participants', 'signedByUser', 'receipts'])
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        return view('livewire.documents.sppd-table', [
            'sppds' => $sppds
        ]);
    }
}
<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use App\Models\Spt;
use App\Models\NotaDinas;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;

#[Lazy]
class SptTable extends Component
{
    public $notaDinasId;

    public $selectedSptId;

    public function mount($notaDinasId = null)
    {
        $this->notaDinasId = $notaDinasId;
    }

    public function selectSpt($sptId)
    {
        $this->selectedSptId = $sptId;
        $this->dispatch('spt-selected', sptId: $sptId);
    }

    public function createSpt($notaDinasId)
    {
        return $this->redirect(route('spt.create', ['nota_dinas_id' => $notaDinasId]));
    }

    #[On('refreshAll')]
    public function refreshData()
    {
        // This will trigger a re-render of the component
    }

    public function confirmDelete($sptId)
    {
        try {
            $spt = Spt::with(['sppds'])->findOrFail($sptId);
            
            // Check if SPT has related SPPDs
            if ($spt->sppds && $spt->sppds->count() > 0) {
                session()->flash('error', 'SPT tidak dapat dihapus karena memiliki SPPD terkait.');
                return;
            }
            
            // Store current state before deletion
            $notaDinasId = $spt->nota_dinas_id;
            
            // Delete the SPT
            $spt->delete();
            
            session()->flash('message', 'SPT berhasil dihapus.');
            
            // Dispatch event to refresh parent component
            $this->dispatch('refreshAll');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus SPT: ' . $e->getMessage());
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
        $spts = $this->notaDinasId 
            ? Spt::where('nota_dinas_id', $this->notaDinasId)
                ->with(['notaDinas.requestingUnit', 'signedByUser', 'sppds'])
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        $notaDinas = $this->notaDinasId 
            ? NotaDinas::find($this->notaDinasId)
            : null;

        return view('livewire.documents.spt-table', [
            'spts' => $spts,
            'notaDinas' => $notaDinas
        ]);
    }
}
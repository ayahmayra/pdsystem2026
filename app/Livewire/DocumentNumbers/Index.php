<?php

namespace App\Livewire\DocumentNumbers;

use App\Models\DocumentNumber;
use App\Models\DocNumberFormat;
use App\Models\NumberSequence;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app', ['title' => 'Riwayat Nomor Dokumen'])]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $docType = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingDocType() { $this->resetPage(); }

    public function render()
    {
        $query = DocumentNumber::with(['format', 'sequence', 'generatedByUser'])
            ->when($this->docType, function($q) {
                $q->where('doc_type', $this->docType);
            })
            ->when($this->search, function($q) {
                $q->where('number', 'like', '%'.$this->search.'%')
                  ->orWhere('doc_id', 'like', '%'.$this->search.'%');
            })
            ->orderByDesc('created_at');
        $numbers = $query->paginate(15);
        $docTypes = DocumentNumber::select('doc_type')->distinct()->pluck('doc_type');
        return view('livewire.document-numbers.index', [
            'numbers' => $numbers,
            'docTypes' => $docTypes,
        ]);
    }
}

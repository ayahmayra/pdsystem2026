<?php

namespace App\Livewire\DocNumberFormats;

use App\Models\DocNumberFormat;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app', ['title' => 'Format Penomoran Dokumen'])]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch() { $this->resetPage(); }

    public function delete(DocNumberFormat $format)
    {
        $format->delete();
        session()->flash('message', 'Format penomoran berhasil dihapus.');
    }

    public function render()
    {
        $formats = DocNumberFormat::query()
            ->when($this->search, function($q) {
                $q->where('doc_type', 'like', '%'.$this->search.'%')
                  ->orWhere('format_string', 'like', '%'.$this->search.'%')
                  ->orWhere('doc_code', 'like', '%'.$this->search.'%');
            })
            ->orderBy('doc_type')
            ->paginate(10);
        return view('livewire.doc-number-formats.index', [
            'formats' => $formats
        ]);
    }
}

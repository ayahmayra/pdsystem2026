<?php

namespace App\Livewire\DocNumberFormats;

use App\Models\DocNumberFormat;
use App\Models\Unit;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public DocNumberFormat $format;

    #[Rule('required|string|max:10')]
    public $doc_type = '';
    #[Rule('nullable|exists:units,id')]
    public $unit_scope_id = '';
    #[Rule('required|string|max:255')]
    public $format_string = '';
    #[Rule('required|string|max:20')]
    public $doc_code = '';
    #[Rule('required|in:NEVER,YEARLY,MONTHLY')]
    public $reset_policy = 'YEARLY';
    #[Rule('required|integer|min:1|max:8')]
    public $padding = 3;
    #[Rule('boolean')]
    public $is_active = true;
    #[Rule('nullable|string|max:255')]
    public $notes = '';

    public function mount(DocNumberFormat $format)
    {
        $this->format = $format;
        $this->doc_type = $format->doc_type;
        $this->unit_scope_id = $format->unit_scope_id;
        $this->format_string = $format->format_string;
        $this->doc_code = $format->doc_code;
        $this->reset_policy = $format->reset_policy;
        $this->padding = $format->padding;
        $this->is_active = $format->is_active;
        $this->notes = $format->notes;
    }

    public function save()
    {
        $this->validate();
        $this->format->update([
            'doc_type' => $this->doc_type,
            'unit_scope_id' => $this->unit_scope_id ?: null,
            'format_string' => $this->format_string,
            'doc_code' => $this->doc_code,
            'reset_policy' => $this->reset_policy,
            'padding' => $this->padding,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
        ]);
        session()->flash('message', 'Format penomoran berhasil diperbarui.');
        return $this->redirect(route('doc-number-formats.index'));
    }

    public function render()
    {
        $units = Unit::orderBy('name')->get();
        return view('livewire.doc-number-formats.edit', [
            'units' => $units
        ]);
    }
}

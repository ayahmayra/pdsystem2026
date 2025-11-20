<?php

namespace App\Livewire\SupportingDocuments;

use App\Models\NotaDinas;
use App\Models\SupportingDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public NotaDinas $notaDinas;
    public SupportingDocument $document;

    #[Rule('required|string|max:255')]
    public $document_type = '';

    #[Rule('required|string|max:255')]
    public $title = '';

    public $description = '';

    #[Rule('nullable|file|max:10240')] // Max 10MB, optional for edit
    public $file;

    public function mount(NotaDinas $notaDinas, SupportingDocument $document)
    {
        $this->notaDinas = $notaDinas;
        $this->document = $document;
        
        // Load existing data
        $this->document_type = $document->document_type;
        $this->title = $document->title;
        $this->description = $document->description;
    }

    public function update()
    {
        $this->validate();

        $data = [
            'document_type' => $this->document_type,
            'title' => $this->title,
            'description' => $this->description,
        ];

        // Handle file upload if new file is provided
        if ($this->file) {
            // Delete old file
            if (Storage::disk('public')->exists($this->document->file_path)) {
                Storage::disk('public')->delete($this->document->file_path);
            }

            // Store new file
            $filePath = $this->file->store('supporting-documents', 'public');
            
            $data = array_merge($data, [
                'file_path' => $filePath,
                'file_name' => $this->file->getClientOriginalName(),
                'file_size' => $this->file->getSize(),
                'mime_type' => $this->file->getMimeType(),
            ]);
        }

        $this->document->update($data);

        session()->flash('message', 'Dokumen pendukung berhasil diperbarui.');

        // Redirect back to documents page with selected state
        $redirectParams = [
            'nota_dinas_id' => $this->notaDinas->id
        ];

        return redirect()->route('documents', $redirectParams);
    }

    public function getBackUrl()
    {
        return route('documents', [
            'nota_dinas_id' => $this->notaDinas->id
        ]);
    }

    public function render()
    {
        return view('livewire.supporting-documents.edit');
    }
}

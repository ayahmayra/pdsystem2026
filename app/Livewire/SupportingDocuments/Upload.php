<?php

namespace App\Livewire\SupportingDocuments;

use App\Models\NotaDinas;
use App\Models\SupportingDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;

class Upload extends Component
{
    use WithFileUploads;

    public NotaDinas $notaDinas;

    #[Rule('required|string|max:255')]
    public $document_type = '';

    #[Rule('required|string|max:255')]
    public $title = '';

    public $description = '';

    #[Rule('required|file|max:10240')] // Max 10MB
    public $file;

    public function mount(NotaDinas $notaDinas)
    {
        $this->notaDinas = $notaDinas;
        
        // Handle delete from query parameter
        $deleteId = request()->query('delete');
        if ($deleteId) {
            $this->deleteDocument($deleteId);
        }
    }

    public function save()
    {
        $this->validate();

        // Store file
        $filePath = $this->file->store('supporting-documents', 'public');

        SupportingDocument::create([
            'nota_dinas_id' => $this->notaDinas->id,
            'document_type' => $this->document_type,
            'title' => $this->title,
            'description' => $this->description,
            'file_path' => $filePath,
            'file_name' => $this->file->getClientOriginalName(),
            'file_size' => $this->file->getSize(),
            'mime_type' => $this->file->getMimeType(),
            'uploaded_by_user_id' => Auth::id(),
            'is_active' => true,
        ]);

        session()->flash('message', 'Dokumen pendukung berhasil diupload.');

        // Redirect back to documents page with selected state
        $redirectParams = [
            'nota_dinas_id' => $this->notaDinas->id
        ];

        return redirect()->route('documents', $redirectParams);
    }

    public function deleteDocument($documentId)
    {
        $document = SupportingDocument::findOrFail($documentId);
        
        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();

        session()->flash('message', 'Dokumen berhasil dihapus.');
        
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
        $documents = $this->notaDinas->supportingDocuments()
            ->with('uploadedByUser')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.supporting-documents.upload', [
            'documents' => $documents,
        ]);
    }
}

<?php

namespace App\Livewire\Users;

use App\Imports\UsersImport;
use App\Helpers\PermissionHelper;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.app', ['title' => 'Import Data Pegawai'])]
class Import extends Component
{
    use WithFileUploads;

    public $file;
    public $employee_type = 'PNS';
    public $isUploading = false;
    public $importResults = null;

    protected $rules = [
        'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        'employee_type' => 'required|in:PNS,PPPK,PPPK PW,Non ASN',
    ];

    protected $messages = [
        'file.required' => 'File Excel harus diupload.',
        'file.mimes' => 'File harus berformat Excel (.xlsx, .xls, atau .csv).',
        'file.max' => 'Ukuran file maksimal 10MB.',
        'employee_type.required' => 'Jenis pegawai harus dipilih.',
        'employee_type.in' => 'Jenis pegawai tidak valid.',
    ];

    public function mount()
    {
        // Check permission
        if (!PermissionHelper::can('users.create')) {
            abort(403, 'Anda tidak memiliki izin untuk mengimport data pegawai.');
        }
    }

    public function updatedFile()
    {
        $this->validateOnly('file');
        $this->importResults = null;
    }

    public function import()
    {
        $this->validate();

        if (!$this->file) {
            session()->flash('error', 'File Excel harus diupload.');
            return;
        }

        $this->isUploading = true;

        try {
            // Store file to a temporary location for import
            $storedPath = $this->file->store('imports', 'local');
            $filePath = storage_path('app/' . $storedPath);

            // Verify file exists
            if (!file_exists($filePath)) {
                throw new \Exception('File tidak dapat diakses setelah upload.');
            }

            // Import data
            $import = new UsersImport($this->employee_type);
            Excel::import($import, $filePath);

            // Get results
            $this->importResults = $import->getImportResults();

            // Clean up temp file
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Prepare success message
            $message = "Import berhasil! ";
            $message .= "Dibuat: {$this->importResults['created']}, ";
            $message .= "Diupdate: {$this->importResults['updated']}, ";
            $message .= "Dilewati: {$this->importResults['skipped']}";

            if (!empty($this->importResults['errors'])) {
                $message .= ". Terdapat " . count($this->importResults['errors']) . " error.";
            }

            session()->flash('message', $message);

            // Reset file
            $this->reset('file');

        } catch (\Exception $e) {
            Log::error('User import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Import gagal: ' . $e->getMessage());
        } finally {
            $this->isUploading = false;
        }
    }


    public function render()
    {
        return view('livewire.users.import');
    }
}

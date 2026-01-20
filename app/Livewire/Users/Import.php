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
            // Pastikan direktori imports ada
            $importsDir = storage_path('app/imports');
            if (!is_dir($importsDir)) {
                mkdir($importsDir, 0755, true);
            }

            // Generate unique filename
            $fileName = 'import_users_' . time() . '_' . uniqid() . '.' . $this->file->getClientOriginalExtension();
            $fullPath = $importsDir . '/' . $fileName;

            // Get temporary file path from Livewire
            $tempPath = $this->file->getRealPath();

            // Log untuk debugging
            Log::info('User import - File upload', [
                'temp_path' => $tempPath,
                'target_path' => $fullPath,
                'temp_exists' => file_exists($tempPath),
                'target_dir_exists' => is_dir($importsDir),
                'target_dir_writable' => is_writable($importsDir),
                'original_name' => $this->file->getClientOriginalName(),
            ]);

            // Verify temp file exists
            if (!$tempPath || !file_exists($tempPath)) {
                throw new \Exception('File temporary tidak ditemukan. Path: ' . ($tempPath ?? 'null'));
            }

            // Copy file dari temp ke target location
            if (!copy($tempPath, $fullPath)) {
                throw new \Exception('Gagal menyalin file dari temporary ke storage. Pastikan direktori storage/app/imports dapat ditulis.');
            }

            // Verify file was copied successfully
            if (!file_exists($fullPath)) {
                throw new \Exception('File tidak ditemukan setelah copy: ' . $fullPath);
            }

            // Import data
            $import = new UsersImport($this->employee_type);
            Excel::import($import, $fullPath);

            // Get results
            $this->importResults = $import->getImportResults();

            // Clean up temp file
            if (file_exists($fullPath)) {
                unlink($fullPath);
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

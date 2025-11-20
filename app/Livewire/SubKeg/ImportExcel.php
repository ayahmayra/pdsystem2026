<?php

namespace App\Livewire\SubKeg;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Imports\SubKegRekeningBelanjaImport;
use App\Exports\SubKegRekeningBelanjaTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.app')]
class ImportExcel extends Component
{
    use WithFileUploads;

    public $excelFile;
    public $isUploading = false;
    public $uploadProgress = 0;
    public $importResults = null;
    public $showResults = false;

    protected $rules = [
        'excelFile' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
    ];

    protected $messages = [
        'excelFile.required' => 'File Excel harus diupload.',
        'excelFile.file' => 'File yang diupload harus berupa file.',
        'excelFile.mimes' => 'File harus berupa Excel (.xlsx, .xls) atau CSV.',
        'excelFile.max' => 'Ukuran file maksimal 10MB.',
    ];

    public function updatedExcelFile()
    {
        $this->validate();
        $this->resetResults();
    }

    public function import()
    {
        $this->validate();

        try {
            $this->isUploading = true;
            $this->uploadProgress = 0;

            // Simpan file ke storage dengan nama unik
            $fileName = 'import_' . time() . '_' . uniqid() . '.' . $this->excelFile->getClientOriginalExtension();
            
            // Pastikan direktori imports ada
            $importsDir = storage_path('app/imports');
            if (!is_dir($importsDir)) {
                mkdir($importsDir, 0755, true);
            }
            
            // Simpan file menggunakan copy dan unlink
            $fullPath = $importsDir . '/' . $fileName;
            $tempPath = $this->excelFile->getRealPath();
            
            // Log sebelum copy
            Log::info('Before copy', [
                'temp_path' => $tempPath,
                'target_path' => $fullPath,
                'temp_exists' => file_exists($tempPath),
                'target_dir_exists' => is_dir($importsDir),
                'target_dir_writable' => is_writable($importsDir),
                'temp_readable' => is_readable($tempPath)
            ]);
            
            // Copy file dari temp ke target
            if (!copy($tempPath, $fullPath)) {
                throw new \Exception('Gagal menyalin file dari ' . $tempPath . ' ke ' . $fullPath);
            }
            
            // Hapus file temp
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            $this->uploadProgress = 30;

            // Pastikan file ada
            if (!file_exists($fullPath)) {
                throw new \Exception('File tidak ditemukan setelah copy: ' . $fullPath);
            }

            // Log untuk debugging
            Log::info('Importing file: ' . $fullPath, [
                'file_exists' => file_exists($fullPath),
                'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0,
                'original_name' => $this->excelFile->getClientOriginalName(),
                'permissions' => fileperms($fullPath)
            ]);

            $this->uploadProgress = 50;

            // Proses import
            $import = new SubKegRekeningBelanjaImport();
            Excel::import($import, $fullPath);

            $this->uploadProgress = 100;

            // Ambil hasil import
            $this->importResults = $import->getImportResults();
            $this->showResults = true;

            // Hapus file setelah import
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Set success message
            $totalProcessed = $this->importResults['sub_keg_created'] + 
                            $this->importResults['sub_keg_updated'] + 
                            $this->importResults['rekening_belanja_created'];

            session()->flash('success', "Import berhasil! Diproses {$totalProcessed} data.");

        } catch (\Exception $e) {
            Log::error('Import Excel error: ' . $e->getMessage(), [
                'file_path' => $fullPath ?? 'unknown',
                'file_exists' => isset($fullPath) ? file_exists($fullPath) : false,
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Terjadi kesalahan saat mengimport file: ' . $e->getMessage());
        } finally {
            $this->isUploading = false;
            $this->uploadProgress = 0;
        }
    }

    public function resetResults()
    {
        $this->importResults = null;
        $this->showResults = false;
        $this->uploadProgress = 0;
    }

    public function downloadTemplate()
    {
        return Excel::download(new SubKegRekeningBelanjaTemplateExport, 'template_sub_keg_rekening_belanja.xlsx');
    }

    public function render()
    {
        return view('livewire.sub-keg.import-excel');
    }
}

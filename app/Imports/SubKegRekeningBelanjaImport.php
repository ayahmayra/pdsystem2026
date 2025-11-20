<?php

namespace App\Imports;

use App\Models\SubKeg;
use App\Models\RekeningBelanja;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubKegRekeningBelanjaImport implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading
{
    private $processedSubKegs = [];
    private $importResults = [
        'sub_keg_created' => 0,
        'sub_keg_updated' => 0,
        'rekening_belanja_created' => 0,
        'rekening_belanja_updated' => 0,
        'errors' => []
    ];

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $index => $row) {
            try {
                // Skip jika data tidak lengkap
                if (empty($row['kode_sub_kegiatan']) || empty($row['nama_sub_kegiatan']) || 
                    empty($row['kode_rekening']) || empty($row['nama_rekening'])) {
                    continue;
                }

                // Bersihkan data
                $kodeSubKeg = trim($row['kode_sub_kegiatan']);
                $namaSubKeg = trim($row['nama_sub_kegiatan']);
                $kodeRekening = trim($row['kode_rekening']);
                $namaRekening = trim($row['nama_rekening']);
                $pagu = $this->parsePagu($row['pagu'] ?? 0);

                // Cari atau buat Sub Kegiatan
                $subKeg = $this->findOrCreateSubKeg($kodeSubKeg, $namaSubKeg);

                // Buat Rekening Belanja
                $this->createRekeningBelanja($subKeg->id, $kodeRekening, $namaRekening, $pagu);

            } catch (\Exception $e) {
                $this->importResults['errors'][] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                Log::error('Import error: ' . $e->getMessage(), ['row' => $row->toArray()]);
            }
        }
    }

    private function findOrCreateSubKeg($kodeSubKeg, $namaSubKeg)
    {
        // Cek cache dulu
        if (isset($this->processedSubKegs[$kodeSubKeg])) {
            return $this->processedSubKegs[$kodeSubKeg];
        }

        // Cari di database
        $subKeg = SubKeg::where('kode_subkeg', $kodeSubKeg)->first();

        if (!$subKeg) {
            // Buat baru
            $subKeg = SubKeg::create([
                'kode_subkeg' => $kodeSubKeg,
                'nama_subkeg' => $namaSubKeg,
                'id_unit' => null,
                'pptk_user_id' => null,
            ]);
            $this->importResults['sub_keg_created']++;
        } else {
            // Update nama jika berbeda
            if ($subKeg->nama_subkeg !== $namaSubKeg) {
                $subKeg->update(['nama_subkeg' => $namaSubKeg]);
                $this->importResults['sub_keg_updated']++;
            }
        }

        // Cache hasil
        $this->processedSubKegs[$kodeSubKeg] = $subKeg;
        return $subKeg;
    }

    private function createRekeningBelanja($subKegId, $kodeRekening, $namaRekening, $pagu)
    {
        // Cek apakah rekening belanja sudah ada dengan kombinasi kode_rekening + sub_keg_id
        // Ini memungkinkan kode rekening yang sama untuk sub kegiatan yang berbeda
        $existingRekening = RekeningBelanja::where('kode_rekening', $kodeRekening)
                                          ->where('sub_keg_id', $subKegId)
                                          ->first();

        if (!$existingRekening) {
            // Buat rekening belanja baru
            RekeningBelanja::create([
                'sub_keg_id' => $subKegId,
                'kode_rekening' => $kodeRekening,
                'nama_rekening' => $namaRekening,
                'pagu' => $pagu,
                'keterangan' => 'Diimpor dari Excel',
                'is_active' => true,
            ]);
            $this->importResults['rekening_belanja_created']++;
            
            Log::info('Rekening Belanja Created', [
                'kode_rekening' => $kodeRekening,
                'sub_keg_id' => $subKegId,
                'created_count' => $this->importResults['rekening_belanja_created']
            ]);
        } else {
            // Update rekening belanja yang sudah ada (kombinasi kode_rekening + sub_keg_id yang sama)
            $updated = false;
            $updateData = [];
            
            // Update nama rekening jika berbeda
            if ($existingRekening->nama_rekening !== $namaRekening) {
                $updateData['nama_rekening'] = $namaRekening;
                $updated = true;
            }
            
            // Update pagu jika berbeda
            if ($existingRekening->pagu != $pagu) {
                $updateData['pagu'] = $pagu;
                $updated = true;
            }
            
            // Update keterangan untuk menunjukkan bahwa data diupdate dari Excel
            $updateData['keterangan'] = 'Diupdate dari Excel - Sub Keg: ' . $subKegId;
            
            // Update jika ada perubahan
            if ($updated) {
                $existingRekening->update($updateData);
                $this->importResults['rekening_belanja_updated']++;
                
                Log::info('Rekening Belanja Updated', [
                    'kode_rekening' => $kodeRekening,
                    'sub_keg_id' => $subKegId,
                    'updated_count' => $this->importResults['rekening_belanja_updated'],
                    'changes' => $updateData
                ]);
            } else {
                Log::info('Rekening Belanja No Changes', [
                    'kode_rekening' => $kodeRekening,
                    'sub_keg_id' => $subKegId,
                    'existing_data' => [
                        'nama_rekening' => $existingRekening->nama_rekening,
                        'pagu' => $existingRekening->pagu
                    ]
                ]);
            }
        }
    }

    private function parsePagu($pagu)
    {
        if (is_numeric($pagu)) {
            return (float) $pagu;
        }

        // Handle format currency
        $pagu = preg_replace('/[^\d.,]/', '', $pagu);
        $pagu = str_replace(',', '.', $pagu);
        
        return is_numeric($pagu) ? (float) $pagu : 0;
    }

    public function rules(): array
    {
        return [
            'kode_sub_kegiatan' => 'required|string|max:255',
            'nama_sub_kegiatan' => 'required|string|max:255',
            'kode_rekening' => 'required|string|max:255',
            'nama_rekening' => 'required|string|max:255',
            'pagu' => 'nullable|numeric|min:0',
        ];
    }


    public function chunkSize(): int
    {
        return 100;
    }

    public function getImportResults()
    {
        return $this->importResults;
    }
}

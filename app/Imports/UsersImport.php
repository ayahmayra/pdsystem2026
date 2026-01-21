<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UsersImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    private $employeeType;
    private $importResults = [
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => []
    ];

    public function __construct($employeeType = 'PNS')
    {
        $this->employeeType = $employeeType;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        // Log available columns from first row for debugging
        if ($collection->isNotEmpty()) {
            $firstRow = $collection->first()->toArray();
            $availableColumns = array_keys($firstRow);
            Log::info('User import - Available columns', [
                'columns' => $availableColumns,
                'first_row_data' => $firstRow
            ]);
        }

        foreach ($collection as $index => $row) {
            try {
                // Normalize column names (case-insensitive, handle spaces)
                $rowArray = $row->toArray();
                $normalizedRow = [];
                foreach ($rowArray as $key => $value) {
                    // Handle null values
                    $value = $value ?? '';
                    $normalizedKey = strtolower(trim(str_replace(' ', '', $key))); // Remove spaces for matching
                    $normalizedRow[$normalizedKey] = $value;
                }

                // Log first row for debugging
                if ($index === 0) {
                    Log::info('User import - First row normalized', [
                        'original_keys' => array_keys($rowArray),
                        'normalized_keys' => array_keys($normalizedRow),
                        'normalized_data' => $normalizedRow
                    ]);
                }

                // Get data from Excel (support multiple column name variations)
                $nama = '';
                $nip = '';
                
                // Try to find nama/name column (multiple variations)
                $namaKeys = ['nama', 'name', 'n a m a', 'n a m e', 'namapegawai', 'namalengkap'];
                foreach ($namaKeys as $key) {
                    $keyNoSpace = str_replace(' ', '', $key);
                    if (isset($normalizedRow[$keyNoSpace])) {
                        $value = trim($normalizedRow[$keyNoSpace] ?? '');
                        if (!empty($value)) {
                            $nama = $value;
                            break;
                        }
                    }
                }
                
                // Try to find nip column (multiple variations)
                $nipKeys = ['nip', 'n i p', 'nomorindukpegawai', 'nomorinduk'];
                foreach ($nipKeys as $key) {
                    $keyNoSpace = str_replace(' ', '', $key);
                    if (isset($normalizedRow[$keyNoSpace])) {
                        $value = trim($normalizedRow[$keyNoSpace] ?? '');
                        if (!empty($value)) {
                            $nip = $value;
                            break;
                        }
                    }
                }
                
                // Get optional fields
                $jabatanKeys = ['jabatan', 'position', 'posisi'];
                $jabatan = '';
                foreach ($jabatanKeys as $key) {
                    $keyNoSpace = str_replace(' ', '', $key);
                    if (isset($normalizedRow[$keyNoSpace])) {
                        $jabatan = trim($normalizedRow[$keyNoSpace] ?? '');
                        if (!empty($jabatan)) break;
                    }
                }
                
                $bidangKeys = ['bidang', 'unit', 'bagian'];
                $bidang = '';
                foreach ($bidangKeys as $key) {
                    $keyNoSpace = str_replace(' ', '', $key);
                    if (isset($normalizedRow[$keyNoSpace])) {
                        $bidang = trim($normalizedRow[$keyNoSpace] ?? '');
                        if (!empty($bidang)) break;
                    }
                }
                
                // Skip empty rows (both nama and nip empty)
                if (empty($nama) && empty($nip)) {
                    continue; // Skip completely empty rows silently
                }
                
                // Error if nama or nip is missing (but not both)
                if (empty($nama) || empty($nip)) {
                    $this->importResults['skipped']++;
                    $missing = [];
                    if (empty($nama)) $missing[] = 'Nama';
                    if (empty($nip)) $missing[] = 'NIP';
                    
                    // Show available columns in error message for debugging
                    $availableCols = array_keys($normalizedRow);
                    $originalCols = array_keys($rowArray);
                    
                    // Build detailed error message
                    $errorMsg = "Baris " . ($index + 2) . ": Kolom " . implode(' dan ', $missing) . " kosong atau tidak ditemukan.";
                    if (!empty($availableCols)) {
                        $errorMsg .= " Kolom yang tersedia di Excel: " . implode(', ', $originalCols);
                    }
                    if (!empty($normalizedRow)) {
                        $errorMsg .= " (Data: " . json_encode(array_slice($normalizedRow, 0, 4)) . ")";
                    }
                    
                    $this->importResults['errors'][] = $errorMsg;
                    Log::warning('User import - Missing required field', [
                        'row' => $index + 2,
                        'missing' => $missing,
                        'original_columns' => $originalCols,
                        'normalized_columns' => array_keys($normalizedRow),
                        'row_data' => $normalizedRow
                    ]);
                    continue;
                }

                // Find or create unit
                $unitId = null;
                if (!empty($bidang)) {
                    $unit = Unit::where('name', 'like', '%' . $bidang . '%')->first();
                    if (!$unit) {
                        // Create new unit if not found
                        $unit = Unit::create([
                            'code' => Str::upper(Str::substr($bidang, 0, 10)),
                            'name' => $bidang,
                            'parent_id' => null,
                        ]);
                        Log::info('Unit created during import', ['unit' => $unit->name]);
                    }
                    $unitId = $unit->id;
                }

                // Check if user already exists by NIP
                $existingUser = User::where('nip', $nip)->first();

                // Prepare user data
                $userData = [
                    'name' => $nama,
                    'nip' => $nip,
                    'employee_type' => $this->employeeType,
                    'position_desc' => $jabatan ?: null,
                    'unit_id' => $unitId,
                    'is_non_staff' => false,
                ];

                // Generate email if not exists
                if ($existingUser && $existingUser->email) {
                    $userData['email'] = $existingUser->email;
                } else {
                    // Generate email from name
                    $emailBase = Str::slug(Str::lower($nama), '');
                    $email = $emailBase . '@' . config('app.domain', 'example.com');
                    
                    // Make sure email is unique
                    $counter = 1;
                    while (User::where('email', $email)->exists()) {
                        $email = $emailBase . $counter . '@' . config('app.domain', 'example.com');
                        $counter++;
                    }
                    $userData['email'] = $email;
                }

                // Set default password if new user
                if (!$existingUser) {
                    $userData['password'] = Hash::make('password123'); // Default password
                }

                if ($existingUser) {
                    // Update existing user
                    $existingUser->update($userData);
                    $this->importResults['updated']++;
                    Log::info('User updated during import', ['nip' => $nip, 'name' => $nama]);
                } else {
                    // Create new user
                    User::create($userData);
                    $this->importResults['created']++;
                    Log::info('User created during import', ['nip' => $nip, 'name' => $nama]);
                }

            } catch (\Exception $e) {
                $this->importResults['skipped']++;
                $this->importResults['errors'][] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                Log::error('User import error', [
                    'row' => $row->toArray(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
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

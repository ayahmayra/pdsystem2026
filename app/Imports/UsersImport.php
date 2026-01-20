<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UsersImport implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading
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
        foreach ($collection as $index => $row) {
            try {
                // Normalize column names (case-insensitive, handle spaces)
                $rowArray = $row->toArray();
                $normalizedRow = [];
                foreach ($rowArray as $key => $value) {
                    $normalizedKey = strtolower(trim($key));
                    $normalizedRow[$normalizedKey] = $value;
                }

                // Get data from Excel (support multiple column name variations)
                $nama = trim($normalizedRow['nama'] ?? $normalizedRow['name'] ?? '');
                $nip = trim($normalizedRow['nip'] ?? '');
                $jabatan = trim($normalizedRow['jabatan'] ?? $normalizedRow['position'] ?? '');
                $bidang = trim($normalizedRow['bidang'] ?? $normalizedRow['unit'] ?? '');
                
                if (empty($nama) || empty($nip)) {
                    $this->importResults['skipped']++;
                    $this->importResults['errors'][] = "Baris " . ($index + 2) . ": Nama atau NIP kosong";
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

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:20',
            'jabatan' => 'nullable|string|max:255',
            'bidang' => 'nullable|string|max:255',
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

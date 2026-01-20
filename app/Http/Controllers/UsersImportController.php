<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersImportController extends Controller
{
    public function downloadTemplate()
    {
        $data = [
            ['Nama', 'NIP', 'Jabatan', 'Bidang'],
            ['Ahmad Suryadi', '198505152010121001', 'Kepala Bidang', 'Bidang IT'],
            ['Siti Nurhaliza', '199203042018031002', 'Staff', 'Bidang Perencanaan'],
        ];

        return Excel::download(
            new class($data) implements FromArray, WithHeadings {
                private $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function array(): array
                {
                    return array_slice($this->data, 1); // Skip header
                }

                public function headings(): array
                {
                    return $this->data[0]; // Return header
                }
            },
            'template_import_pegawai.xlsx'
        );
    }
}

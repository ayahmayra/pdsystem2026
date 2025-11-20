<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubKegRekeningBelanjaTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * @return array
     */
    public function array(): array
    {
        return [
            [
                '5.02.02.2.01.0001',
                'Koordinasi dan Penyusunan KUA dan PPAS',
                '5.1.02.01.01.0001',
                'Belanja Pegawai - Gaji Pokok PNS',
                500000000
            ],
            [
                '5.02.02.2.01.0001',
                'Koordinasi dan Penyusunan KUA dan PPAS',
                '5.1.02.01.01.0002',
                'Belanja Pegawai - Tunjangan Kinerja',
                200000000
            ],
            [
                '5.02.02.2.02.0001',
                'Koordinasi, Penyusunan dan Verifikasi RKA SKPD',
                '5.1.02.01.01.0003',
                'Belanja Barang - Alat Tulis Kantor',
                50000000
            ],
            [
                '5.02.02.2.02.0001',
                'Koordinasi, Penyusunan dan Verifikasi RKA SKPD',
                '5.1.02.01.01.0004',
                'Belanja Barang - Bahan Habis Pakai',
                75000000
            ],
            [
                '5.02.03.2.01.0001',
                'Penatausahaan Pembiayaan Daerah',
                '5.1.02.01.01.0005',
                'Belanja Modal - Peralatan Kantor',
                300000000
            ],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Kode Sub Kegiatan',
            'Nama Sub Kegiatan',
            'Kode Rekening',
            'Nama Rekening',
            'Pagu'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '366092']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20, // Kode Sub Kegiatan
            'B' => 40, // Nama Sub Kegiatan
            'C' => 20, // Kode Rekening
            'D' => 40, // Nama Rekening
            'E' => 15, // Pagu
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class GlobalRekapExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $rekapData;

    public function __construct($rekapData)
    {
        $this->rekapData = $rekapData;
    }

    public function collection()
    {
        return collect($this->rekapData);
    }

    public function headings(): array
    {
        return [
            'No. Nota Dinas',
            'Tanggal Nota Dinas',
            'Asal',
            'Tujuan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Durasi (Hari)',
            'No. SPT',
            'Tanggal SPT',
            'Penandatangan SPT',
            'No. SPPD',
            'Tanggal SPPD',
            'Penandatangan SPPD',
            'Alat Angkutan',
            'Nama PPTK',
            'No. Laporan',
            'Tanggal Laporan',
            'Nama Peserta',
            'NIP Peserta',
            'Pangkat/Golongan',
            'No. Kwitansi',
            'Tanggal Kwitansi',
            'Total Kwitansi',
            'Transportasi - Uraian',
            'Transportasi - Nilai',
            'Transportasi - Deskripsi',
            'Penginapan - Uraian',
            'Penginapan - Nilai',
            'Penginapan - Deskripsi',
            'Uang Harian - Uraian',
            'Uang Harian - Nilai',
            'Uang Harian - Deskripsi',
            'Representatif - Uraian',
            'Representatif - Nilai',
            'Representatif - Deskripsi',
            'Biaya Lainnya - Uraian',
            'Biaya Lainnya - Nilai',
            'Biaya Lainnya - Deskripsi',
            'Dokumen Pendukung'
        ];
    }

    public function map($item): array
    {
        // Handle receipt lines data
        $transportLines = $item['receipt_lines']['transport'] ?? [];
        $lodgingLines = $item['receipt_lines']['lodging'] ?? [];
        $perdiemLines = $item['receipt_lines']['perdiem'] ?? [];
        $representationLines = $item['receipt_lines']['representation'] ?? [];
        $otherLines = $item['receipt_lines']['other'] ?? [];

        // Get first item from each category for main row
        $transportLine = !empty($transportLines) ? $transportLines[0] : null;
        $lodgingLine = !empty($lodgingLines) ? $lodgingLines[0] : null;
        $perdiemLine = !empty($perdiemLines) ? $perdiemLines[0] : null;
        $representationLine = !empty($representationLines) ? $representationLines[0] : null;
        $otherLine = !empty($otherLines) ? $otherLines[0] : null;

        // Format supporting documents
        $supportingDocs = '';
        if (isset($item['supporting_documents']) && $item['supporting_documents']) {
            $docs = [];
            foreach ($item['supporting_documents'] as $doc) {
                $docs[] = $doc['name'] ?? $doc['file_name'] ?? 'Unknown';
            }
            $supportingDocs = implode('; ', $docs);
        }

        return [
            $item['number'] ?? '',
            $item['date'] ? \Carbon\Carbon::parse($item['date'])->format('d/m/Y') : '',
            $item['origin'] ?? '',
            $item['destination'] ?? '',
            $item['start_date'] ? \Carbon\Carbon::parse($item['start_date'])->format('d/m/Y') : '',
            $item['end_date'] ? \Carbon\Carbon::parse($item['end_date'])->format('d/m/Y') : '',
            $item['duration'] ?? '',
            $item['spt_number'] ?? '',
            $item['spt_date'] ? \Carbon\Carbon::parse($item['spt_date'])->format('d/m/Y') : '',
            $item['spt_signer'] ?? '',
            $item['sppd_number'] ?? '',
            $item['sppd_date'] ? \Carbon\Carbon::parse($item['sppd_date'])->format('d/m/Y') : '',
            $item['sppd_signer'] ?? '',
            $item['transport_mode'] ?? '',
            $item['pptk_name'] ?? '',
            $item['trip_report_number'] ?? '',
            $item['trip_report_date'] ? \Carbon\Carbon::parse($item['trip_report_date'])->format('d/m/Y') : '',
            $item['participant_name'] ?? '',
            $item['participant_nip'] ?? '',
            $item['participant_rank'] ?? '',
            $item['receipt_number'] ?? '',
            $item['receipt_date'] ? \Carbon\Carbon::parse($item['receipt_date'])->format('d/m/Y') : '',
            $item['receipt_total'] ? number_format($item['receipt_total'], 0, ',', '.') : '',
            // Transport
            $transportLine ? $this->formatReceiptLine($transportLine) : '',
            $transportLine ? number_format($transportLine['line_total'] ?? 0, 0, ',', '.') : '',
            $transportLine['desc'] ?? '',
            // Lodging
            $lodgingLine ? $this->formatReceiptLine($lodgingLine) : '',
            $lodgingLine ? number_format($lodgingLine['line_total'] ?? 0, 0, ',', '.') : '',
            $lodgingLine['desc'] ?? '',
            // Perdiem
            $perdiemLine ? $this->formatReceiptLine($perdiemLine) : '',
            $perdiemLine ? number_format($perdiemLine['line_total'] ?? 0, 0, ',', '.') : '',
            $perdiemLine['desc'] ?? '',
            // Representation
            $representationLine ? $this->formatReceiptLine($representationLine) : '',
            $representationLine ? number_format($representationLine['line_total'] ?? 0, 0, ',', '.') : '',
            $representationLine['desc'] ?? '',
            // Other
            $otherLine ? $this->formatReceiptLine($otherLine) : '',
            $otherLine ? number_format($otherLine['line_total'] ?? 0, 0, ',', '.') : '',
            $otherLine['desc'] ?? '',
            $supportingDocs
        ];
    }

    private function formatReceiptLine($line)
    {
        if (is_array($line)) {
            $qty = $line['qty'] ?? 0;
            $unitAmount = $line['unit_amount'] ?? 0;
            
            // Special handling for lodging 30%
            if (isset($line['no_lodging']) && $line['no_lodging'] && isset($line['reference_rate'])) {
                return "({$qty} x (30% x Rp " . number_format($line['reference_rate'], 0, ',', '.') . "))";
            }
            
            return "({$qty} x Rp " . number_format($unitAmount, 0, ',', '.') . ")";
        }
        
        return '';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // No. Nota Dinas
            'B' => 15, // Tanggal Nota Dinas
            'C' => 15, // Asal
            'D' => 15, // Tujuan
            'E' => 15, // Tanggal Mulai
            'F' => 15, // Tanggal Selesai
            'G' => 10, // Durasi
            'H' => 20, // No. SPT
            'I' => 15, // Tanggal SPT
            'J' => 25, // Penandatangan SPT
            'K' => 20, // No. SPPD
            'L' => 15, // Tanggal SPPD
            'M' => 25, // Penandatangan SPPD
            'N' => 15, // Alat Angkutan
            'O' => 20, // Nama PPTK
            'P' => 15, // No. Laporan
            'Q' => 15, // Tanggal Laporan
            'R' => 25, // Nama Peserta
            'S' => 20, // NIP Peserta
            'T' => 20, // Pangkat/Golongan
            'U' => 15, // No. Kwitansi
            'V' => 15, // Tanggal Kwitansi
            'W' => 15, // Total Kwitansi
            'X' => 25, // Transportasi - Uraian
            'Y' => 15, // Transportasi - Nilai
            'Z' => 30, // Transportasi - Deskripsi
            'AA' => 25, // Penginapan - Uraian
            'AB' => 15, // Penginapan - Nilai
            'AC' => 30, // Penginapan - Deskripsi
            'AD' => 25, // Uang Harian - Uraian
            'AE' => 15, // Uang Harian - Nilai
            'AF' => 30, // Uang Harian - Deskripsi
            'AG' => 25, // Representatif - Uraian
            'AH' => 15, // Representatif - Nilai
            'AI' => 30, // Representatif - Deskripsi
            'AJ' => 25, // Biaya Lainnya - Uraian
            'AK' => 15, // Biaya Lainnya - Nilai
            'AL' => 30, // Biaya Lainnya - Deskripsi
            'AM' => 30, // Dokumen Pendukung
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'E3F2FD',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Apply borders to all cells with data
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                
                $sheet->getStyle('A1:' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                
                // Auto-fit row heights
                for ($row = 1; $row <= $lastRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }
                
                // Freeze header row
                $sheet->freezePane('A2');
            },
        ];
    }
}

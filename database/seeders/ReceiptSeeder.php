<?php

namespace Database\Seeders;

use App\Models\Receipt;
use App\Models\ReceiptLine;
use App\Models\Sppd;
use App\Models\TravelGrade;
use App\Services\DocumentNumberService;
use Illuminate\Database\Seeder;

class ReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sppds = Sppd::with(['user', 'spt.notaDinas'])->get();

        if ($sppds->isEmpty()) {
            return;
        }

        foreach ($sppds as $sppd) {
            // Skip if user doesn't have travel grade
            if (!$sppd->user->travel_grade_id) {
                continue;
            }

            // Create receipt for each SPPD
            $docNumberResult = DocumentNumberService::generate('KW', $sppd->number_scope_unit_id, now(), [
                'sppd_id' => $sppd->id,
                'user_id' => $sppd->user_id,
            ], $sppd->user_id);

            $receipt = Receipt::create([
                'doc_no' => $docNumberResult['number'],
                'number_is_manual' => false,
                'number_format_id' => $docNumberResult['format']->id ?? null,
                'number_sequence_id' => $docNumberResult['sequence']->id ?? null,
                'number_scope_unit_id' => $sppd->number_scope_unit_id,
                'sppd_id' => $sppd->id,
                'receipt_date' => now(),
                'travel_grade_id' => $sppd->user->travel_grade_id,
                'payee_user_id' => $sppd->user_id,
                'total_amount' => 0, // Will be calculated from receipt lines
                'notes' => 'Kwitansi untuk SPPD ' . $sppd->doc_no,
            ]);

            // Create receipt lines
            $totalAmount = 0;

            // Transportation line
            $transportAmount = rand(50000, 200000);
            ReceiptLine::create([
                'receipt_id' => $receipt->id,
                'component_type' => 'TRANSPORT',
                'description' => 'Biaya transportasi',
                'amount' => $transportAmount,
            ]);
            $totalAmount += $transportAmount;

            // Lodging line
            $lodgingAmount = rand(100000, 300000);
            ReceiptLine::create([
                'receipt_id' => $receipt->id,
                'component_type' => 'LODGING',
                'description' => 'Biaya penginapan',
                'amount' => $lodgingAmount,
            ]);
            $totalAmount += $lodgingAmount;

            // Perdiem line
            $perdiemAmount = rand(50000, 150000);
            ReceiptLine::create([
                'receipt_id' => $receipt->id,
                'component_type' => 'PERDIEM',
                'description' => 'Uang harian',
                'amount' => $perdiemAmount,
            ]);
            $totalAmount += $perdiemAmount;

            // Update total amount
            $receipt->update(['total_amount' => $totalAmount]);
        }
    }
}

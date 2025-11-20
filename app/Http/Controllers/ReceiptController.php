<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\PermissionHelper;

class ReceiptController extends Controller
{
    public function generatePdf(Receipt $receipt)
    {
        // Check if user can view receipts
        if (!PermissionHelper::can('receipts.view')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat kwitansi.');
        }

        // Check unit scope for bendahara pengeluaran pembantu
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId && $receipt->sppd->spt->notaDinas->requesting_unit_id != $userUnitId) {
                abort(403, 'Anda hanya dapat melihat kwitansi dari bidang Anda.');
            }
        }

        // Load relationships yang diperlukan untuk PDF kwitansi lengkap
        $receipt->load([
            'sppd.spt.notaDinas.participants.user',
            'sppd.spt.notaDinas.originPlace',
            'sppd.spt.notaDinas.destinationCity',
            'sppd.spt.notaDinas.destinationCity.province',
            'sppd.signedByUser.position',
            'sppd.signedByUser.unit',
            'sppd.signedByUser.rank',
            'sppd.subKeg.unit',
            'sppd.subKeg.pptkUser.position',
            'sppd.subKeg.pptkUser.unit',
            'sppd.subKeg.pptkUser.rank',
            'payeeUser.position',
            'payeeUser.rank',
            'payeeUser.unit',
            'treasurerUser.position',
            'treasurerUser.unit',
            'treasurerUser.rank',
            'travelGrade',
            'lines',
            'rekeningBelanja'
        ]);
        
        // Generate PDF
        $pdf = Pdf::loadView('receipts.pdf', [
            'receipt' => $receipt
        ]);
        
        // Set paper size dan orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Set options untuk hasil yang lebih baik
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
            'chroot' => public_path(),
        ]);
        
        // Tampilkan preview PDF di browser menggunakan stream() dengan Attachment => false
        $filename = 'Kwitansi_' . ($receipt->receipt_no ?: 'Manual') . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->stream("$filename", ["Attachment" => false]);
    }
    
    public function downloadPdf(Receipt $receipt)
    {
        // Check if user can view receipts
        if (!PermissionHelper::can('receipts.view')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat kwitansi.');
        }

        // Check unit scope for bendahara pengeluaran pembantu
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId && $receipt->sppd->spt->notaDinas->requesting_unit_id != $userUnitId) {
                abort(403, 'Anda hanya dapat melihat kwitansi dari bidang Anda.');
            }
        }

        // Load relationships yang diperlukan untuk PDF kwitansi lengkap
        $receipt->load([
            'sppd.spt.notaDinas.participants.user',
            'sppd.spt.notaDinas.originPlace',
            'sppd.spt.notaDinas.destinationCity',
            'sppd.spt.notaDinas.destinationCity.province',
            'sppd.signedByUser.position',
            'sppd.signedByUser.unit',
            'sppd.signedByUser.rank',
            'sppd.subKeg.unit',
            'sppd.subKeg.pptkUser.position',
            'sppd.subKeg.pptkUser.unit',
            'sppd.subKeg.pptkUser.rank',
            'payeeUser.position',
            'payeeUser.rank',
            'payeeUser.unit',
            'treasurerUser.position',
            'treasurerUser.unit',
            'treasurerUser.rank',
            'travelGrade',
            'lines',
            'rekeningBelanja'
        ]);
        
        // Generate PDF
        $pdf = Pdf::loadView('receipts.pdf', [
            'receipt' => $receipt
        ]);
        
        // Set paper size dan orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Set options untuk hasil yang lebih baik
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
            'chroot' => public_path(),
        ]);
        
        // Download PDF dengan nama file yang sesuai
        $filename = 'Kwitansi_' . ($receipt->receipt_no ?: 'Manual') . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Spt;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SptController extends Controller
{
    public function generatePdf(Spt $spt)
    {
        // Load relationships yang diperlukan
        $spt->load(['notaDinas.participants.user', 'signedByUser', 'sppds']);
        
        // Generate PDF
        $pdf = Pdf::loadView('spt.pdf', [
            'spt' => $spt
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
        $filename = 'Surat_Perintah_Tugas_' . str_replace(['/', '\\'], '-', $spt->doc_no) . '.pdf';
        
        return $pdf->stream("$filename", ["Attachment" => false]);
    }
    
    public function downloadPdf(Spt $spt)
    {
        // Load relationships yang diperlukan
        $spt->load(['notaDinas.participants.user', 'signedByUser', 'sppds']);
        
        // Generate PDF
        $pdf = Pdf::loadView('spt.pdf', [
            'spt' => $spt
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
        $filename = 'Surat_Perintah_Tugas_' . str_replace(['/', '\\'], '-', $spt->doc_no) . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}

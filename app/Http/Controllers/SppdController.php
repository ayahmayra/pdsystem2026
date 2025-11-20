<?php
namespace App\Http\Controllers;
use App\Models\Sppd;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
class SppdController extends Controller
{
    public function generatePdf(Sppd $sppd)
    {
        // Load relationships yang diperlukan dengan eager loading yang benar
        $sppd->load([
            'spt.notaDinas.originPlace', 
            'spt.notaDinas.destinationCity', 
            'itineraries',
            'signedByUser.position',
            'signedByUser.unit',
            'signedByUser.rank',
            'subKeg.unit',
            'subKeg.pptkUser.position',
            'subKeg.pptkUser.unit',
            'subKeg.pptkUser.rank'
        ]);
        // Generate PDF
        $pdf = Pdf::loadView('sppd.pdf', [
            'sppd' => $sppd
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
        $filename = 'Surat_Perintah_Perjalanan_Dinas_' . str_replace(['/', '\\'], '-', $sppd->doc_no) . '.pdf';
        return $pdf->stream("$filename", ["Attachment" => false]);
    }
    public function downloadPdf(Sppd $sppd)
    {
        // Load relationships yang diperlukan dengan eager loading yang benar
        $sppd->load([
            'spt.notaDinas.originPlace', 
            'spt.notaDinas.destinationCity', 
            'itineraries',
            'signedByUser.position',
            'signedByUser.unit',
            'signedByUser.rank',
            'subKeg.unit',
            'subKeg.pptkUser.position',
            'subKeg.pptkUser.unit',
            'subKeg.pptkUser.rank'
        ]);
        // Generate PDF
        $pdf = Pdf::loadView('sppd.pdf', [
            'sppd' => $sppd
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
        $filename = 'Surat_Perintah_Perjalanan_Dinas_' . str_replace(['/', '\\'], '-', $sppd->doc_no) . '_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
}

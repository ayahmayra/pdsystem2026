<?php

if (!function_exists('formatActivitiesForPdf')) {
    function formatActivitiesForPdf($text) {
        if (empty($text)) {
            return '-';
        }
        
        // Convert markdown to HTML for PDF
        $formatted = $text;
        
        // Convert **bold** to <strong>
        $formatted = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $formatted);
        
        // Convert *italic* to <em>
        $formatted = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $formatted);
        
        // Convert bullet points and numbering to HTML list
        $lines = explode("\n", $formatted);
        $formattedLines = [];
        $inBulletList = false;
        $inNumberedList = false;
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            if (preg_match('/^•\s*(.+)$/', $trimmedLine, $matches)) {
                // Bullet point
                if (!$inBulletList) {
                    $formattedLines[] = '<ul>';
                    $inBulletList = true;
                    $inNumberedList = false;
                }
                $formattedLines[] = '<li>' . $matches[1] . '</li>';
            } elseif (preg_match('/^\d+\.\s*(.+)$/', $trimmedLine, $matches)) {
                // Numbered list
                if (!$inNumberedList) {
                    $formattedLines[] = '<ol>';
                    $inNumberedList = true;
                    $inBulletList = false;
                }
                $formattedLines[] = '<li>' . $matches[1] . '</li>';
            } else {
                // Regular text - close any open lists
                if ($inBulletList) {
                    $formattedLines[] = '</ul>';
                    $inBulletList = false;
                }
                if ($inNumberedList) {
                    $formattedLines[] = '</ol>';
                    $inNumberedList = false;
                }
                
                // Add regular text
                if (!empty($trimmedLine)) {
                    $formattedLines[] = '<p>' . $trimmedLine . '</p>';
                } else {
                    $formattedLines[] = '<br>';
                }
            }
        }
        
        // Close any remaining open lists
        if ($inBulletList) {
            $formattedLines[] = '</ul>';
        }
        if ($inNumberedList) {
            $formattedLines[] = '</ol>';
        }
        
        return implode("\n", $formattedLines);
    }
}

if (!function_exists('terbilang')) {
    function terbilang($angka) {
        $angka = abs($angka);
        $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $terbilang = "";
        
        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = terbilang($angka - 10) . " belas";
        } elseif ($angka < 100) {
            $terbilang = terbilang(intval($angka/10)) . " puluh" . terbilang($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = " seratus" . terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = terbilang(intval($angka/100)) . " ratus" . terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = " seribu" . terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = terbilang(intval($angka/1000)) . " ribu" . terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = terbilang(intval($angka/1000000)) . " juta" . terbilang($angka % 1000000);
            
        }
        
        return $terbilang; 
    }
}

if (!function_exists('terbilangTanggal')) {
    function terbilangTanggal($tanggal) {
        if (empty($tanggal)) {
            return [
                'hari' => '.........',
                'tanggal' => '.........',
                'bulan' => '.........',
                'tahun' => '.........'
            ];
        }
        
        $date = \Carbon\Carbon::parse($tanggal);
        $hari = $date->locale('id')->translatedFormat('l');
        $tanggal_angka = $date->day;
        $bulan = $date->locale('id')->translatedFormat('F');
        $tahun = $date->year;
        
        // Konversi tanggal ke terbilang
        $tanggal_terbilang = ucwords(trim(terbilang($tanggal_angka)));
        
        // Konversi tahun ke terbilang
        $tahun_terbilang = ucwords(trim(terbilang($tahun)));
        
        return [
            'hari' => $hari,
            'tanggal' => $tanggal_terbilang,
            'bulan' => $bulan,
            'tahun' => $tahun_terbilang
        ];
    }
}

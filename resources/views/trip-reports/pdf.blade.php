<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perjalanan Dinas - {{ $tripReport->report_no ?? 'LAP-' . $tripReport->id }}</title>
    <style>
        @page { size: A4; margin-right: 15mm; margin-left: 15mm; margin-top: 15mm; margin-bottom: 10mm; }
        @page:first { margin-right: 15mm; margin-left: 15mm; margin-top: 5mm; margin-bottom: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.5; margin: 0; padding: 0; }
        p, h1, h2, h3, h4, h5, h6, table, th, td, li { line-height: 1; }
        .container { width: 100%; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 4mm; }
        .header table { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: middle; padding: 0; }
        .logo { height: 20mm; max-width: 100%; }
        .header-text { text-align: center; }
        .header-text h1 { font-size: 14pt; margin: 0 0 1pt 0; text-transform: uppercase; letter-spacing: 0.5pt; }
        .header-text h3 { font-size: 12pt; margin: 0 0 1pt 0; text-transform: uppercase; letter-spacing: 0.5pt; }
        .header-text .unit { font-size: 12pt; font-weight: 700; margin: 0 0 2pt 0; text-transform: uppercase; }
        .header-text p { font-size: 9pt; margin: 1pt 0; }
        .document-title { font-size: 11pt; font-weight: bold; text-align: center; margin: 2mm 0 6mm 0;  }
        .document-number { font-size: 11pt; text-align: center; margin: 0 0 6mm 0; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 4mm; }
        .info-table td { padding: 1pt 0; vertical-align: top; }
        .info-table .label { width: 50px;  }
        .info-table .separator { width: 10px; }
        .info-table .content { padding-left: 3pt; }
        .divider { border-bottom: 3px solid #000; margin: 3mm 0 4mm 0; }
        .content-table { width: 100%; border-collapse: collapse; margin-bottom: 2mm; }
        .content-table td { padding: 0.5pt 0; vertical-align: top; }
        .content-table .number { width: 15px; }
        .content-table .label { width: 180px;  }
        .content-table .separator { width: 8px; }
        .content-table .content { padding-left: 3pt; text-transform: capitalize; }
        .activities { margin-top: 5px; padding: 8px;     }
        .activities ul, .activities ol { margin: 5px 0; padding-left: 20px; }
        .activities ul { list-style-type: disc; }
        .activities ol { list-style-type: decimal; }
        .activities li { margin: 3px 0; line-height: 1.4; }
        .activities p { margin: 4px 0; line-height: 1.4; }
        .activities strong, .activities b { font-weight: bold; }
        .activities em, .activities i { font-style: italic; }
        .activities h1, .activities h2, .activities h3 { margin: 8px 0 4px 0; font-weight: bold; }
        .activities h1 { font-size: 16px; }
        .activities h2 { font-size: 15px; }
        .activities h3 { font-size: 14px; }
        .closing { margin: 2mm 0; text-align: justify; }
        .signature { margin-top: 8mm; page-break-inside: avoid; text-align: right; }
        .signature .block { display: inline-block; text-align: left; }
        .signature div { margin-bottom: 1pt;line-height: 1; }
        .signature .name { font-weight: bold; text-decoration: underline; }
        .signature .rank, .signature .nip { font-size: 12pt; line-height: 1;}
        
        /* Kontrol page-break agar rapi di multi-halaman */
        table { page-break-inside: auto; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        .end-section { page-break-inside: avoid; }

    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table style="border-bottom: 2px solid black;">
                <tr>
                    <td style="width: 22mm;">
                        <img src="{{ public_path('logobengkalis.png') }}" alt="Logo" class="logo">
                    </td>
                    <td class="header-text" >
                        <h3>PEMERINTAH KABUPATEN BENGKALIS</h3>
                        <h1>{{ \DB::table('org_settings')->value('name') }}</h1>
                        <p>{{ \DB::table('org_settings')->value('address') }}</p>
                        <p>Telepon {{ \DB::table('org_settings')->value('phone') }} e-mail : {{ \DB::table('org_settings')->value('email') }}</p>
                    </td>
                    <td style="width: 22mm;"></td>
                </tr>
            </table>
        </div>
        
        <div class="document-title">LAPORAN HASIL PERJALANAN DINAS</div>
        @if($tripReport->report_no)
            <div class="document-number">NOMOR: {{ $tripReport->report_no }}</div>
        @endif

        <!-- Pembuka -->
        <div class="closing">
            @php
                $tanggal_terbilang = terbilangTanggal($tripReport->report_date);
            @endphp
            <p>Pada hari ini {{ $tanggal_terbilang['hari'] }} tanggal {{ $tanggal_terbilang['tanggal'] }} bulan {{ $tanggal_terbilang['bulan'] }} tahun {{ $tanggal_terbilang['tahun'] }} yang bertanda tangan dibawah ini :</p>
        </div>

        <!-- Informasi Peserta -->
        @php
            $sortedParticipants = $tripReport->spt->notaDinas->getSortedParticipants();
        @endphp
        @if($sortedParticipants && $sortedParticipants->count() > 0)
            @foreach($sortedParticipants as $index => $participant)
                <table class="content-table">
                    <tr>
                        <td class="number">{{ $index + 1 }}.</td>
                        <td class="label">Nama</td>
                        <td class="separator">:</td>
                        <td class="content">{{ $participant->user_name_snapshot ?: $participant->user?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="number"></td>
                        <td class="label">NIP</td>
                        <td class="separator">:</td>
                        <td class="content">{{ $participant->user_nip_snapshot ?: $participant->user?->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="number"></td>
                        <td class="label">Pangkat/Gol</td>
                        <td class="separator">:</td>
                        <td class="content">{{ $participant->user_rank_name_snapshot ?: $participant->user?->rank?->name ?? '-' }} ({{ $participant->user_rank_code_snapshot ?: $participant->user?->rank?->code ?? '-' }})</td>
                    </tr>
                    <tr>
                        <td class="number"></td>
                        <td class="label">Jabatan</td>
                        <td class="separator">:</td>
                        <td class="content">{{ $participant->user_position_desc_snapshot ?: ($participant->user?->position_desc ?: $participant->user?->position?->name ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td class="number"></td>
                        <td class="label">Satuan Kerja</td>
                        <td class="separator">:</td>
                        <td class="content">{{ $participant->user_unit_name_snapshot ?: $participant->user?->unit?->name ?? '-' }}</td>
                    </tr>
                </table>
                @if($index < $sortedParticipants->count() - 1)
                    <div style="height: 2mm;"></div>
                @endif
            @endforeach
        @else
            <!-- Fallback jika tidak ada participant -->
            <table class="content-table">
                <tr>
                    <td class="number">1.</td>
                    <td class="label">Nama</td>
                    <td class="separator">:</td>
                    <td class="content">{{ $tripReport->createdByUser->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="number"></td>
                    <td class="label">NIP</td>
                    <td class="separator">:</td>
                    <td class="content">{{ $tripReport->createdByUser->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="number"></td>
                    <td class="label">Pangkat/Gol</td>
                    <td class="separator">:</td>
                    <td class="content">{{ $tripReport->createdByUser->rank?->name ?? '-' }} ({{ $tripReport->createdByUser->rank?->code ?? '-' }})</td>
                </tr>
                <tr>
                    <td class="number"></td>
                    <td class="label">Jabatan</td>
                    <td class="separator">:</td>
                    <td class="content">{{ $tripReport->createdByUser->position?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="number"></td>
                    <td class="label">Satuan Kerja</td>
                    <td class="separator">:</td>
                    <td class="content">{{ $tripReport->createdByUser->unit?->name ?? '-' }}</td>
                </tr>
            </table>
        @endif

        <!-- Dasar Surat Tugas -->
        <div class="closing">
            <p>Berdasarkan Surat Tugas Nomor: <strong>{{ $tripReport->spt->doc_no ?? '-' }}</strong> Tanggal <strong>{{ $tripReport->spt->start_date ? \Carbon\Carbon::parse($tripReport->spt->start_date)->locale('id')->translatedFormat('d F Y') : '-' }}</strong> melaksanakan Perjalanan Dinas ke <strong>{{ $tripReport->spt->notaDinas->destinationCity->name ?? '-' }}</strong></p>
        </div>

        <!-- Laporan Pelaksanaan -->
        <div class="closing">
            <p>Bersama ini dapat dilaporkan pelaksanaan perjalanan dinas sebagai berikut:</p>
        </div>

        <!-- Detail Perjalanan -->
        <div class="closing">
            <p>1. Berangkat dari <strong>{{ $tripReport->spt->notaDinas->originPlace->name ?? '.........' }}</strong> menuju <strong>{{ $tripReport->spt->notaDinas->destinationCity->name ?? '.........' }}</strong> pada tanggal <strong>{{ $tripReport->spt->notaDinas->start_date ? \Carbon\Carbon::parse($tripReport->spt->notaDinas->start_date)->locale('id')->translatedFormat('d F Y') : '.........' }}</strong></p>
            
            <p>2. Kegiatan dan Hasil Perjalanan Dinas:</p>
            <div class="activities" style="margin-left: 20px; margin-top: 5px;">
                {!! formatActivitiesForPdf($tripReport->activities) !!}
            </div>
            
            <p>3. Kembali ke <strong>{{ $tripReport->spt->notaDinas->originPlace->name ?? '.........' }}</strong> pada tanggal <strong>{{ $tripReport->spt->notaDinas->end_date ? \Carbon\Carbon::parse($tripReport->spt->notaDinas->end_date)->locale('id')->translatedFormat('d F Y') : '.........' }}</strong></p>
        </div>

        <!-- Penutup -->
        <div class="closing">
            <p>Demikian laporan hasil perjalanan dinas ini dibuat dan untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature" style="text-align: right; margin-top: 30px;">
            <div style="max-width: 500px; margin-left: auto;">
                <p style="margin-bottom: 20px; font-weight: bold;">Yang Membuat Laporan:</p>
                
                @if($tripReport->spt->notaDinas->participants && $tripReport->spt->notaDinas->participants->count() > 0)
                    <table style="width: 100%; border-collapse: collapse;">
                        @foreach($tripReport->spt->notaDinas->getSortedParticipants() as $index => $participant)
                            <tr>
                                <td style="width: 30px; padding: 15px 0; vertical-align: top;">{{ $index + 1 }}.</td>
                                <td style="padding: 15px 0; vertical-align: top;">{{ $participant->user->gelar_depan ?? '' }} {{ $participant->user->name ?? 'N/A' }} {{ $participant->user->gelar_belakang ?? '' }}</td>
                                <td style="width: 150px; padding: 15px 0; vertical-align: top; border-bottom: 1px solid black;"></td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <!-- Fallback jika tidak ada participant -->
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 30px; padding: 15px 0; vertical-align: top;">1.</td>
                            <td style="padding: 15px 0; vertical-align: top;">{{ $tripReport->createdByUser->gelar_depan ?? '' }} {{ $tripReport->createdByUser->name ?? 'N/A' }} {{ $tripReport->createdByUser->gelar_belakang ?? '' }}</td>
                            <td style="width: 150px; padding: 15px 0; vertical-align: top; border-bottom: 1px solid black;"></td>
                        </tr>
                    </table>
                @endif
            </div>
        </div>
    </div>
</body>
</html>

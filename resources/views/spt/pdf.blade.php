<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perintah Tugas - {{ $spt->doc_no }}</title>
    <style>
        @page { size: A4; margin-right: 15mm; margin-left: 15mm; margin-top: 15mm; margin-bottom: 10mm; }
        @page:first { margin-right: 15mm; margin-left: 15mm; margin-top: 5mm; margin-bottom: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 11.5pt; line-height: 1.5; margin: 0; padding: 0; }
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
        .content-table { width: 100%; border-collapse: collapse; margin-bottom: 4mm; }
        .content-table td { padding: 1pt 0; vertical-align: top; }
        .content-table .number { width: 15px; }
        .content-table .label { width: 180px;  }
        .content-table .separator { width: 8px; }
        .content-table .content { padding-left: 3pt; text-transform: capitalize; }
        .members-table { width: 100%; border-collapse: collapse; margin-bottom: 4mm; }
        .members-table th, .members-table td { border: 1px solid black; padding: 2pt; vertical-align: top; }
        .members-table th { background-color: #f3f4f6; font-weight: 600; text-align: center; }
        .members-table .no { width: 15pt; text-align: center; }
        .closing { margin: 4mm 0; text-align: justify; }
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
        
        <div class="document-title">SURAT TUGAS</div>
        <div class="document-number">NOMOR: {{ $spt->doc_no }}</div>
        
        <!-- Dasar -->
        <table class="content-table">
            <tr>
                <td class="label" style="width: 120px;">Dasar</td>
                <td class="separator">:</td>
                <td class="content">
                    @if($spt->notaDinas)
                        <table style="width: 100%; border: none; border-collapse: collapse;">
                            <tr>
                                <td style="width: 20px; vertical-align: top; border: none; padding: 0;">1.</td>
                                <td style="border: none; padding-bottom: 10px;">Dokumen Pelaksanaan Anggaran Badan Pengelolaan Keuangan dan Aset Daerah Kabupaten Bengkalis</td>
                            </tr>
                            <tr>
                                <td style="width: 20px; vertical-align: top; border: none; padding: 0;">2.</td>
                                <td style="border: none; padding: 0;">Nota Dinas {{ $spt->notaDinas->fromUser?->position?->name ?? '-' }} {{ $spt->notaDinas->fromUser?->unit?->name ?? '-' }} Nomor: {{ $spt->notaDinas->doc_no }}, Tanggal {{ $spt->notaDinas->nd_date ? \Carbon\Carbon::parse($spt->notaDinas->nd_date)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                        </table>
                         
                    @else
                        {{ $spt->notes ?? '-' }}
                    @endif
                </td>
            </tr>
        </table>
        
        <div style="text-align: center; font-weight: bold; margin: 4mm 0;">MEMERINTAHKAN</div>
        
        <!-- Kepada -->
        <table class="content-table">
            <tr>
                <td class="label" style="width: 120px;">Kepada</td>
                <td class="separator">:</td>
                <td class="content">
                    @php
                        $ordered = $spt->getSortedParticipantsSnapshot();
                    @endphp
                    @if($ordered->count() > 0)
                        
                            <table style="width: 100%; border: none; border-collapse: collapse; margin-bottom: 10px;">
                                                        @foreach($ordered as $i => $participant)
                        @php
                            $name = $participant['name'] ?? '-';
                            $rankName = $participant['rank_name'] ?? '';
                            $rankCode = $participant['rank_code'] ?? '';
                            $nip = $participant['nip'] ?? '';
                            $position = $participant['position_desc'] ?: ($participant['position_name'] ?? '');
                        @endphp
                        <tr>
                            <td>{{ $i+1 }}.</td>
                            <td style="width: 150px; vertical-align: top; border: none; padding: 2px 0;">Nama</td>
                            <td style="width: 10px; vertical-align: top; border: none; padding: 2px 0;">:</td>
                            <td style="border: none; padding: 2px 0;">{{ $name }}</td>
                        </tr>
                        @if($rankName || $rankCode)
                        <tr>
                            <td></td>
                            <td style="width: 150px; vertical-align: top; border: none; padding: 2px 0;">Pangkat/Gol. Ruang</td>
                            <td style="width: 10px; vertical-align: top; border: none; padding: 2px 0;">:</td>
                            <td style="border: none; padding: 2px 0;">{{ $rankName }} {{ $rankCode ? '(' . $rankCode . ')' : '' }}</td>
                        </tr>
                        @endif
                        @if($nip)
                        <tr>
                            <td></td>
                            <td style="width: 150px; vertical-align: top; border: none; padding: 2px 0;">NIP</td>
                            <td style="width: 10px; vertical-align: top; border: none; padding: 2px 0;">:</td>
                            <td style="border: none; padding: 2px 0;">{{ $nip }}</td>
                        </tr>
                        @endif
                        @if($position)
                        <tr>
                            <td></td>
                            <td style="width: 150px; vertical-align: top; border: none; padding: 2px 0;">Jabatan</td>
                            <td style="width: 10px; vertical-align: top; border: none; padding: 2px 0;">:</td>
                            <td style="border: none; padding: 2px 0;">{{ $position }}</td>
                        </tr>
                        @endif
                       
                        @if(!$loop->last)<br>@endif
                    @endforeach
                    </table>
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>
        
        <!-- Untuk -->
        <table class="content-table">
            <tr>
                <td class="label" style="width: 120px;">Untuk</td>
                <td class="separator">:</td>
                <td class="content" >
                    @if($spt->notaDinas)

                    <table style="width: 100%; border: none; border-collapse: collapse;">
                        <tr>
                            <td style="width: 20px; vertical-align: top; border: none; padding: 0;">1.</td>
                            <td style="border: none; padding-bottom: 10px; text-align: justify;">{{ $spt->notaDinas->maksud }}</td>
                        </tr>
                        <tr>
                            <td style="width: 20px; vertical-align: top; border: none; padding: 0;">2.</td>
                            <td style="border: none; border: none; padding-bottom: 10px;">Lamanya perjalanan : {{ $spt->notaDinas->start_date && $spt->notaDinas->end_date ? \Carbon\Carbon::parse($spt->notaDinas->start_date)->diffInDays(\Carbon\Carbon::parse($spt->notaDinas->end_date)) + 1 : '-' }} ({{ $spt->notaDinas->start_date && $spt->notaDinas->end_date ? ucfirst(\NumberFormatter::create('id', \NumberFormatter::SPELLOUT)->format(\Carbon\Carbon::parse($spt->notaDinas->start_date)->diffInDays(\Carbon\Carbon::parse($spt->notaDinas->end_date)) + 1)) : '-' }}) hari PP dari Tgl. {{ $spt->notaDinas->start_date ? \Carbon\Carbon::parse($spt->notaDinas->start_date)->locale('id')->translatedFormat('d F Y') : '-' }} s/d {{ $spt->notaDinas->end_date ? \Carbon\Carbon::parse($spt->notaDinas->end_date)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 20px; vertical-align: top; border: none; padding: 0;">3.</td>
                            <td style="border: none; padding-bottom: 10px;">Setelah melaksanakan tugas paling lama 5 (Lima) hari menyampaikan laporan tertulis kepada pimpinan</td>
                        </tr>
                    </table>

                         
                      
                    @else
                        {{ $spt->assignment_title ?? '-' }}
                    @endif
                </td>
            </tr>
        </table>
        
        <!-- Tanda Tangan -->
        <div class="end-section" style="page-break-inside: avoid;">
            <div class="signature">
                <div class="block" style="width: 300px;">
                    <div>Bengkalis, {{ $spt->spt_date ? \Carbon\Carbon::parse($spt->spt_date)->locale('id')->translatedFormat('d F Y') : '-' }}</div>
                    @php
                        // Deteksi apakah assignment_title adalah custom atau auto
                        $defaultTitle = $spt->signedByUser?->position_desc ?: ($spt->signedByUser?->position?->name ?? '');
                        $isCustomAssignment = !empty(trim($spt->assignment_title)) && trim($spt->assignment_title) !== trim($defaultTitle);
                    @endphp
                    
                    @if($isCustomAssignment)
                        <!-- Custom assignment title -->
                        <div style="word-wrap: break-word; white-space: normal;">{!! nl2br(e($spt->assignment_title)) !!}</div>
                    @else
                        <!-- Auto assignment title (dari snapshot Nota Dinas) -->
                        @php
                            $signedByUserSnapshot = $spt->getSignedByUserSnapshot();
                            $positionName = $signedByUserSnapshot['position_name'] ?? $spt->signedByUser?->position?->name ?? '-';
                            $unitName = $signedByUserSnapshot['unit_name'] ?? $spt->signedByUser?->unit?->name ?? '';
                        $positionDesc = $signedByUserSnapshot['position_desc'] ?? $spt->signedByUser?->position_desc ?? '';
                        @endphp
                        @if($positionDesc)
                            <!-- Jika ada position_desc, tampilkan position_desc -> unit_name -> organisation name -->
                            <div style="word-wrap: break-word; white-space: normal;">{{ $positionDesc }}
                          
                            <div>{{ \DB::table('org_settings')->value('name') }}</div>
                        @elseif($unitName)
                            <!-- Jika ada unit name, tampilkan dalam baris terpisah -->
                            <div style="word-wrap: break-word; white-space: normal;">{{ $positionName }} {{ $unitName }}</div>
                           
                            {{-- <div>{{ \DB::table('org_settings')->value('name') }}</div> --}}
                        @else
                            <!-- Jika tidak ada unit name, position langsung disambung dengan organisasi -->
                            <div style="word-wrap: break-word; white-space: normal;">{{ $positionName }} {{ \DB::table('org_settings')->value('name') }}</div>
                        @endif
                        <div>Kabupaten Bengkalis</div>             
                    @endif
                    
                    <br><br><br><br><br>
                    <div class="name">{{ $spt->getSignedByUserSnapshot()['gelar_depan'] ?? $spt->signedByUser?->gelar_depan ?? '-' }} {{ $spt->getSignedByUserSnapshot()['name'] ?? $spt->signedByUser?->name ?? '-' }} {{ $spt->getSignedByUserSnapshot()['gelar_belakang'] ?? $spt->signedByUser?->gelar_belakang ?? '-' }}</div>
                    <div class="rank">{{ $spt->getSignedByUserSnapshot()['rank_name'] ?? $spt->signedByUser?->rank?->name ?? '-' }} ({{ $spt->getSignedByUserSnapshot()['rank_code'] ?? $spt->signedByUser?->rank?->code ?? '-' }})</div>
                    <div class="nip">NIP. {{ $spt->getSignedByUserSnapshot()['nip'] ?? $spt->signedByUser?->nip ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

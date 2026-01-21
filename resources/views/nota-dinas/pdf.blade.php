<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Dinas - {{ $notaDinas->doc_no }}</title>
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
        .header-text h1 { font-size: 13pt; margin: 0 0 1pt 0; text-transform: uppercase; letter-spacing: 0.3pt; white-space: nowrap; }
        .header-text h3 { font-size: 12pt; margin: 0 0 1pt 0; text-transform: uppercase; letter-spacing: 0.5pt; }
        .header-text .unit { font-size: 12pt; font-weight: 700; margin: 0 0 2pt 0; text-transform: uppercase; }
        .header-text p { font-size: 9pt; margin: 1pt 0; }
        .document-title { font-size: 14pt; font-weight: bold; text-align: center; margin: 2mm 0 6mm 0;  }
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
        .participants-table { width: 100%; border-collapse: collapse; margin-bottom: 4mm; }
        .participants-table th, .participants-table td { border: 1px solid black; padding: 2pt; vertical-align: top; }
        .participants-table th { background-color: #f3f4f6; font-weight: 600; text-align: center; }
        .participants-table .no { width: 15pt; text-align: center; }
        .participants-table td:nth-child(2) { width: 40%; }
        .participants-table td:nth-child(3) { width: 40%; }
        .participants-table td:nth-child(4) { width: 15%; }
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
            <table style="border-bottom: 4px solid black;">
                <tr>
                    <td style="width: 22mm;">
                        <img src="{{ public_path('logobengkalis.png') }}" alt="Logo" class="logo">
                    </td>
                    <td class="header-text" >
                        <h3>PEMERINTAH KABUPATEN BENGKALIS</h3>
                        <h1>{{ \DB::table('org_settings')->value('name') }}</h1>
                        <p>{{ \DB::table('org_settings')->value('address') }}</p>
                        <p>Telepon {{ \DB::table('org_settings')->value('phone') }} e-mail : {{ \DB::table('org_settings')->value('email') }}</p>
                        <br>
                    <div style="margin-bottom: 8px;"></div>
                    </td>
                    <td style="width: 22mm;"></td>
                </tr>
            </table>
        </div>
        
        <div class="document-title">NOTA DINAS</div>
        
        <!-- Info Surat -->
        <table class="info-table">
            <tr>
                <td class="label">Yth.</td>
                <td class="separator">:</td>
                <td class="content">
                    @php
                        $toPositionDesc = $notaDinas->to_user_position_desc_snapshot ?: $notaDinas->toUser?->position_desc;
                        $toPositionName = $notaDinas->to_user_position_name_snapshot ?: $notaDinas->toUser?->position?->name ?? '-';
                        $toUnitName = $notaDinas->to_user_unit_name_snapshot ?: $notaDinas->toUser?->unit?->name;
                        $toInstansiName = $notaDinas->toUser?->getInstansiName();
                    @endphp
                    
                    @if($toPositionDesc)
                        {{ $toPositionDesc }}
                    @else
                        {{ $toPositionName }}
                        @if($toUnitName) {{ $toUnitName }} @endif
                        @if($toInstansiName) {{ $toInstansiName }} @endif
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Dari</td>
                <td class="separator">:</td>
                <td class="content">
                    @php
                        $fromPositionDesc = $notaDinas->from_user_position_desc_snapshot ?: $notaDinas->fromUser?->position_desc;
                        $fromPositionName = $notaDinas->from_user_position_name_snapshot ?: $notaDinas->fromUser?->position?->name ?? '-';
                        $fromUnitName = $notaDinas->from_user_unit_name_snapshot ?: $notaDinas->fromUser?->unit?->name;
                        $fromInstansiName = $notaDinas->fromUser?->getInstansiName();
                    @endphp
                    
                    @if($notaDinas->custom_signer_title)
                        {{ $notaDinas->custom_signer_title }}
                    @elseif($fromPositionDesc)
                        {{ $fromPositionDesc }}
                    @else
                        {{ $fromPositionName }}
                        @if($fromUnitName) {{ $fromUnitName }} @endif
                        @if($fromInstansiName) {{ $fromInstansiName }} @endif
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Tembusan</td>
                <td class="separator">:</td>
                <td class="content">{{ $notaDinas->tembusan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal</td>
                <td class="separator">:</td>
                <td class="content">{{ $notaDinas->nd_date ? \Carbon\Carbon::parse($notaDinas->nd_date)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nomor</td>
                <td class="separator">:</td>
                <td class="content">{{ $notaDinas->doc_no ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Sifat</td>
                <td class="separator">:</td>
                <td class="content">{{ $notaDinas->sifat ?? 'Biasa' }}</td>
            </tr>
            <tr>
                <td class="label">Lampiran</td>
                <td class="separator">:</td>
                <td class="content">{{ $notaDinas->lampiran_count ? $notaDinas->lampiran_count . ' lembar' : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Hal</td>
                <td class="separator">:</td>
                <td class="content">{{ $notaDinas->hal }}</td>
            </tr>
        </table>
        
        <div class="divider" style="border-bottom: 1.5px solid #000; margin: 3mm 0 4mm 0;"></div>
        
        <!-- Isi Surat -->
        <div class="closing">
            <p>Bersama ini diajukan rencana Perjalanan Dinas kepada 
            @if($toPositionDesc)
                {{ $toPositionDesc }}
            @else
                {{ $toPositionName }}
                @if($toUnitName) {{ $toUnitName }} @endif
                @if($toInstansiName) {{ $toInstansiName }} @endif
            @endif
            dengan ketentuan sebagai berikut :</p>
        </div>
        
        <table class="content-table">
            @php $itemNumber = 1; @endphp
            
            @if($notaDinas->dasar)
            <tr>
                <td class="number">{{ $itemNumber++ }}.</td>
                <td class="label">Dasar</td>
                <td class="separator">:</td>
                <td class="content" style="text-align: justify; padding-bottom: 10px;">{{ $notaDinas->dasar }}</td>
            </tr>
            <tr>
               <td ></td>
            </tr>
            @endif
            
            <tr>
                <td class="number">{{ $itemNumber++ }}.</td>
                <td class="label">Maksud</td>
                <td class="separator">:</td>
                <td class="content"  style="text-align: justify; padding-bottom: 10px;">{{ $notaDinas->maksud }}</td>
            </tr>
            <tr>
                <td ></td>
             </tr>
            <tr>
                <td class="number">{{ $itemNumber++ }}.</td>
                <td class="label">Tujuan</td>
                <td class="separator">:</td>
                <td class="content" >{{ $notaDinas->destinationCity?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td ></td>
             </tr>
            <tr>
                <td class="number">{{ $itemNumber++ }}.</td>
                <td class="label">Lamanya Perjalanan</td>
                <td class="separator">:</td>
                <td class="content">{{ $notaDinas->start_date && $notaDinas->end_date ? \Carbon\Carbon::parse($notaDinas->start_date)->diffInDays(\Carbon\Carbon::parse($notaDinas->end_date)) + 1 : '-' }} hari PP dari Tgl. {{ $notaDinas->start_date ? \Carbon\Carbon::parse($notaDinas->start_date)->locale('id')->translatedFormat('d F Y') : '-' }} s/d {{ $notaDinas->end_date ? \Carbon\Carbon::parse($notaDinas->end_date)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td ></td>
             </tr>
            <tr>
                <td class="number">{{ $itemNumber++ }}.</td>
                <td class="label">Yang Bepergian</td>
                <td class="separator">:</td>
                <td class="content"></td>
            </tr>
        </table>
        
        <!-- Tabel Peserta -->
        <table class="participants-table">
            <thead>
                <tr>
                    <th class="no">No</th>
                    <th>Nama/NIP/Pangkat</th>
                    <th>Jabatan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $ordered = $notaDinas->getSortedParticipants();
                @endphp
                @forelse($ordered as $i => $p)
                    <tr>
                        <td class="no">{{ $i+1 }}</td>
                        <td style="min-width: 220px; max-width: 380px; word-break: break-word;">
                            {{ $p->user_name_snapshot ?: $p->user->name ?? '-' }}<br>
                            @if($p->user_rank_name_snapshot ?: $p->user->rank?->name)
                                {{ $p->user_rank_name_snapshot ?: $p->user->rank?->name }}@if($p->user_rank_code_snapshot ?: $p->user->rank?->code) ({{ $p->user_rank_code_snapshot ?: $p->user->rank?->code }})@endif
                                <br>
                            @endif
                            @if($p->user_nip_snapshot ?: $p->user->nip ?? null)
                            {{-- nip pegawai --}}
                                {{ $p->user?->getNipLabel() }}. {{ $p->user_nip_snapshot ?: $p->user->nip }}
                            @else
                                <br>
                            @endif
                        </td>
                        <td>
                            @php
                                $pPositionDesc = $p->user_position_desc_snapshot ?: $p->user->position_desc;
                                $pPositionName = $p->user_position_name_snapshot ?: $p->user->position?->name ?? '-';
                                $pUnitName = $p->user_unit_name_snapshot ?: $p->user->unit?->name;
                                $pInstansiName = $p->user?->getInstansiName();
                            @endphp
                            
                            @if($pPositionDesc)
                                {{ $pPositionDesc }}
                            @else
                                {{ $pPositionName }}
                                @if($pUnitName) {{ $pUnitName }} @endif
                                @if($pInstansiName) {{ $pInstansiName }} @endif
                            @endif
                        </td>
                        <td></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #666;">Tidak ada peserta</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Penutup -->
        <div class="end-section" style="page-break-inside: avoid;">
            <div class="closing">
                <p>Demikian disampaikan, atas bantuan dan persetujuan Bapak diucapkan terima kasih.</p>
            </div>
            
            <!-- Tanda Tangan -->
            <div class="signature">
                <div class="block" style="max-width: 250px; word-wrap: break-word; overflow-wrap: break-word;">
                    @php
                        $signerCustomTitle = $notaDinas->custom_signer_title;
                        $signerPositionDesc = $notaDinas->from_user_position_desc_snapshot ?: $notaDinas->fromUser?->position_desc;
                        $signerPositionName = $notaDinas->from_user_position_name_snapshot ?: $notaDinas->fromUser?->position?->name ?? '-';
                        $signerUnitName = $notaDinas->from_user_unit_name_snapshot ?: $notaDinas->fromUser?->unit?->name;
                        $signerInstansiName = $notaDinas->fromUser?->getInstansiName() ?? \DB::table('org_settings')->value('name');
                    @endphp
                    
                    @if($signerCustomTitle)
                        <div style="word-wrap: break-word; white-space: normal; max-width: 100%;">{{ $signerCustomTitle }}</div>
                    @elseif($signerPositionDesc)
                        <div style="word-wrap: break-word; white-space: normal; max-width: 100%;">{{ $signerPositionDesc }}</div>
                    @else
                        <div style="word-wrap: break-word; white-space: normal; max-width: 100%;">
                            {{ $signerPositionName }}
                            @if($signerUnitName) {{ $signerUnitName }}@endif
                            @if($signerInstansiName) {{ $signerInstansiName }}@endif
                        </div>
                    @endif
                    
                    <br><br><br><br><br>
                    <div class="name" style="max-width: 100%; word-wrap: break-word;">{{ $notaDinas->from_user_gelar_depan_snapshot ?: $notaDinas->fromUser?->gelar_depan ?? '' }} {{ $notaDinas->from_user_name_snapshot ?: $notaDinas->fromUser?->name ?? '-' }} {{ $notaDinas->from_user_gelar_belakang_snapshot ?: $notaDinas->fromUser?->gelar_belakang ?? '' }}</div>
                    <div class="rank" style="max-width: 100%; word-wrap: break-word;">
                        @php
                            $signerRankName = $notaDinas->from_user_rank_name_snapshot ?: $notaDinas->fromUser?->rank?->name;
                            $signerRankCode = $notaDinas->from_user_rank_code_snapshot ?: $notaDinas->fromUser?->rank?->code;
                        @endphp
                        @if($signerRankName){{ $signerRankName }}@if($signerRankCode) ({{ $signerRankCode }})@endif
@else-@endif
                    </div>
                    <div class="nip" style="max-width: 100%; word-wrap: break-word;">{{ $notaDinas->fromUser?->getNipLabel() }}. {{ $notaDinas->from_user_nip_snapshot ?: $notaDinas->fromUser?->nip ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

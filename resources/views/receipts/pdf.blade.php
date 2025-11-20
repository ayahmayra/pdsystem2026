<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kwitansi - {{ $receipt->receipt_no ?? 'Manual' }}</title>
  <style>
    @page { size: A4; margin: 15mm 12mm; }
    body { font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.3; color:#000; }
    .topbar{display:flex;justify-content:flex-end;font-size:10pt;margin-bottom:4px}
    .header{text-align:center;margin:1px 0 6px}
    .title{font-weight:bold;letter-spacing:4px;font-size:14pt}
    /* === BAGIAN KWITANSI (2 kolom menggunakan tabel) === */
    .kwitansi-table{width:100%;border-collapse:collapse;border:1px solid #000}
    .kwitansi-table td{border-right:1px solid #000;padding:6px;vertical-align:top}
    .kwitansi-table td:first-child{text-align:center;width:35%}
    .kwitansi-table td:last-child{border-right:none;width:65%}
    .row{display:table;width:100%}
    .col-label{font-weight:bold;font-size:8pt;width:8mm;display:table-cell}
    .col-content{display:table-cell;padding-left:4px}
    .block{margin-bottom:12px;min-height:80px;display:flex;flex-direction:column;justify-content:space-between}
    .block:last-child{margin-bottom:0}
    .block-title{font-weight:bold;margin-bottom:4px;font-size:10pt}
    .muted{font-size:9pt}
    .name{text-decoration:underline;font-weight:bold;font-size:10pt}
    .text-center{text-align:center}
    .text-right{text-align:right}
    .mt-4{margin-top:8px}
    .mt-6{margin-top:12px}
    .mt-8{margin-top:16px}
    .kv-line{display:table;width:100%}
    .kv-line > div{display:table-cell}
    .kv-line > div:first-child{width:25mm}
    .dots{display:inline-block;border-bottom:1px dotted #000;width:100%;height:.8em;vertical-align:bottom}
    .divider{height:1px;background-color:#000;margin:8px 0}
    /* === TABEL KOLOM KEDUA === */
    .right-table{width:100%;border:none}
    .right-table td{padding:4px 0;vertical-align:top;border:none;text-align:left}
    .right-table .label-cell{width:35mm;border:none;text-align:left}
    .right-table .content-cell{padding-left:8px;border:none;text-align:left}
    .right-table .amount-cell{text-align:left !important;padding-left:0 !important;width:auto !important}
    /* === TABEL PERHITUNGAN SPPD RAMPUNG === */
    table{width:100%;border-collapse:collapse}
    .table{margin-top:8px}
    .table th,.table td{border:1px solid #000;padding:4px;font-size:9pt;vertical-align:top}
    .table th{text-align:center;font-weight:bold}
    /* === TANDA TANGAN BAWAH === */
    .signatures{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px}
    .sign-box{text-align:center}
    .sign-place{margin:8px 0 32px}
  </style>
</head>
<body>
@php
  $nd = $receipt->sppd->spt?->notaDinas;
  $kota = $nd?->destinationCity?->name;
  $prov = $nd?->destinationCity?->province?->name;
  $maksud = $nd?->maksud;
  $tahun = $receipt->receipt_date ? \Carbon\Carbon::parse($receipt->receipt_date)->year : now()->year;
  $bulanTahun = $receipt->receipt_date
      ? \Carbon\Carbon::parse($receipt->receipt_date)->locale('id')->translatedFormat('F Y')
      : '-';
  function money_id($n){ return 'Rp ' . number_format((float)($n ?? 0), 0, ',', '.'); }
  
  // Function to check if lodging is "tidak menginap" using database field
  function isNoLodging($line, $receipt) {
    if ($line->component !== 'LODGING') return false;
    
    // Use the no_lodging field from database if available
    if (isset($line->no_lodging)) {
      return (bool)$line->no_lodging;
    }
    
    // Fallback: detect based on amount (for backward compatibility)
    $referenceRate = getReferenceRate($line, $receipt);
    
    if (!$referenceRate) return false;
    
    // Check if current amount is approximately 30% of reference rate
    $expectedAmount = $referenceRate * 0.3;
    $tolerance = 100; // 100 rupiah tolerance untuk mengatasi floating point issues
    
    // Additional check: make sure it's significantly less than full rate
    $isSignificantlyLess = $line->unit_amount < ($referenceRate * 0.5);
    
    return $isSignificantlyLess && abs($line->unit_amount - $expectedAmount) <= $tolerance;
  }
  
  // Function to get reference rate for display
  function getReferenceRate($line, $receipt) {
    if ($line->component !== 'LODGING') return null;
    
    // Use snapshot if available (preferred for consistency)
    if (isset($line->reference_rate_snapshot) && $line->reference_rate_snapshot > 0) {
      return $line->reference_rate_snapshot;
    }
    
    // Fallback to real-time calculation for backward compatibility
    $notaDinas = $receipt->sppd->spt->notaDinas;
    $destinationCity = $notaDinas->destinationCity;
    $travelGradeId = $receipt->travel_grade_id;
    
    if (!$destinationCity || !$travelGradeId) return null;
    
    $referenceRateService = new \App\Services\ReferenceRateService();
    return $referenceRateService->getLodgingCap($destinationCity->province_id, $travelGradeId);
  }

  // Debug: Tampilkan struktur data lines
  $lines = collect($receipt->lines ?? []);
  
  // Debug: Tampilkan data lines untuk troubleshooting
  // dd($lines->toArray());
  
  // Debug: Tampilkan informasi receipt (uncomment untuk debug)
  // echo "<!-- Debug: Receipt ID: " . $receipt->id . " -->";
  // echo "<!-- Debug: Total lines: " . $lines->count() . " -->";
  // echo "<!-- Debug: Travel Grade ID: " . ($receipt->travel_grade_id ?? 'null') . " -->";
  // if ($lines->count() > 0) {
  //   echo "<!-- Debug: Sample line: " . json_encode($lines->first()) . " -->";
  // }
  
  // Gunakan field category yang baru untuk pengelompokan yang lebih mudah
  $byCat = fn($keys)=>$lines->whereIn('category',(array)$keys)->sum('line_total');
  $transport = [
    'laut'  => $byCat(['transport']), // Semua transport akan masuk ke sini
    'darat' => $byCat(['transport']), // Semua transport akan masuk ke sini
    'roro'  => $byCat(['transport']), // Semua transport akan masuk ke sini
    'udara' => $byCat(['transport']), // Semua transport akan masuk ke sini
    'taksi' => $byCat(['transport']), // Semua transport akan masuk ke sini
  ];
  $lodgingAmount = $byCat(['lodging']);
  $perDiemAmount = $byCat(['per_diem']);
  $reprAmount    = $byCat(['representation']);
  $lodgingNights = $lines->firstWhere('category','lodging')['qty'] ?? null;
  $perDiemDays   = $lines->firstWhere('component','PERDIEM')['qty'] ?? null;
  $perDiemRate   = $lines->firstWhere('component','PERDIEM')['unit_amount'] ?? null;
  $fmtNights = $lodgingNights ? "({$lodgingNights} Malam)" : "(1 Malam)";
  $fmtPerDiem = ($perDiemDays && $perDiemRate) ? "({$perDiemDays} hari x ".money_id($perDiemRate).")" : "";

  // Kategori transportasi yang dinamis berdasarkan data kwitansi
  $transportCategories = [];
  
  // Debug: Tampilkan semua lines yang ada
  // dd($lines->toArray());
  
  // Debug: Tampilkan receipt lines yang ada
  // dd($receipt->lines);
  
  // Ambil data transportasi yang dinamis berdasarkan data kwitansi
  $transportCategories = [];
  
  if ($lines->count() > 0) {
    // Ambil semua kategori transportasi yang ada pada data kwitansi
    $transportLines = $lines->filter(function($line) {
      return ($line['category'] ?? '') === 'transport';
    });
    
    foreach ($transportLines as $line) {
      $categoryName = '';
      $component = $line['component'] ?? '';
      
      // Mapping kategori yang lebih fleksibel berdasarkan component
      if ($component === 'AIRFARE') {
        $categoryName = 'Tiket Pesawat';
      } elseif ($component === 'INTRA_PROV') {
        $categoryName = 'Transport Dalam Provinsi';
      } elseif ($component === 'INTRA_DISTRICT') {
        $categoryName = 'Transport Dalam Kabupaten';
      } elseif ($component === 'OFFICIAL_VEHICLE') {
        $categoryName = 'Kendaraan Dinas';
      } elseif ($component === 'TAXI') {
        $categoryName = 'Taxi';
      } elseif ($component === 'RORO') {
        $categoryName = 'Kapal RORO';
      } elseif ($component === 'TOLL') {
        $categoryName = 'Tol';
      } elseif ($component === 'PARKIR_INAP') {
        $categoryName = 'Parkir & Penginapan';
      } else {
        // Jika tidak ada yang cocok, gunakan nama component asli
        $categoryName = ucfirst(str_replace(['_'], [' '], $component));
      }
      
      $transportCategories[] = [
        'name' => $categoryName,
        'amount' => $line['line_total'] ?? 0,
        'qty' => $line['qty'] ?? 1,
        'unit' => $line['unit'] ?? '-',
        'original_component' => $component
      ];
    }
  }
  
  // Jika tidak ada data transportasi, tampilkan pesan
  if (empty($transportCategories)) {
    $transportCategories = [
      ['name' => 'Tidak ada data transportasi', 'amount' => 0, 'qty' => 1, 'unit' => '-']
    ];
  }
@endphp

  <div class="topbar">Kas No : {{ $receipt->receipt_no ?? '__________________' }}</div>
  <div class="header"><div class="title">K  W  I  T  A  N  S  I</div></div>

  <!-- === BAGIAN 1: KWITANSI (dua kolom menggunakan tabel) === -->
  <table class="kwitansi-table">
    <tr>
      <!-- Kiri: A, B, C saja -->
      <td>
        <!-- A -->
        <div class="block">
          <div class="row">
            <div class="col-label">A.</div>
            <div class="col-content">
              <div class="block-title">PEMBAYARAN TAHUN DINAS</div>
              <div>TAHUN ANGGARAN {{ $tahun }}</div>
              <div class="mt-4"></div>
              <div class="block-title">KODE REKENING</div>
              <div style="font-size: 12px;">
                @if($receipt->sppd->subKeg)
                  {{ $receipt->sppd->subKeg->kode_subkeg }}
                @endif
                @if($receipt->rekeningBelanja){{ $receipt->rekeningBelanja->kode_rekening }}
                @endif
                @if(!$receipt->sppd->subKeg && !$receipt->rekeningBelanja)
                  -
                @endif
              </div>
              <div class="mt-4"></div>
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <!-- B -->
        <div class="block">
          <div class="row">
            <div class="col-label">B.</div>
            <div class="col-content">
              <div class="block-title">SETUJU DIBAYAR</div>
              @php
                // Cek apakah penandatangan adalah pimpinan organisasi
                $orgHeadUserId = \DB::table('org_settings')->value('head_user_id');
                $isOrgHead = $receipt->sppd->signed_by_user_id == $orgHeadUserId;
              @endphp
              <div class="muted">{{ $isOrgHead ? 'PENGGUNA ANGGARAN' : 'KUASA PENGGUNA ANGGARAN' }}</div>
             
              <div class="mt-6"></div>
              <div class="mt-6"></div>
              <div class="mt-6"></div>
              <div class="name">{{ $receipt->sppd->signedByUser->fullNameWithTitles() ?? '-' }}</div>
              <div class="muted">NIP. {{ $receipt->sppd->signedByUser->nip ?? '-' }}</div>
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <!-- C -->
        <div class="block">
          <div class="row">
            <div class="col-label">C.</div>
            <div class="col-content">
              <div class="block-title">LUNAS DIBAYAR</div>
              <div class="muted">PADA TGL.</div>
              <div class="muted">{{ $receipt->treasurer_title ?? 'Bendahara Pengeluaran Pembantu' }}</div>
              
              <div class="mt-6"></div>
              <div class="mt-6"></div>
              <div class="mt-6"></div>
              <div class="name">{{ $receipt->getTreasurerUserSnapshot()['name'] ?? ($receipt->treasurerUser?->fullNameWithTitles() ?? '-') }}</div>
              <div class="muted">NIP. {{ $receipt->getTreasurerUserSnapshot()['nip'] ?? ($receipt->treasurerUser?->nip ?? '-') }}</div>
            </div>
          </div>
        </div>
      </td>

      <!-- Kanan: SUDAH TERIMA DARI, UANG SEBESAR, Y A I T U, Tanggal/Nomor, "Yang Terima" -->
      <td>
        <!-- SUDAH TERIMA DARI -->
        <table class="right-table">
          <tr>
            <td class="label-cell">SUDAH TERIMA DARI</td>
       
            <td>: KUASA PENGGUNA ANGGARAN</td>
          </tr>
        </table>

        <div class="mt-4"></div>

        <!-- UANG SEBESAR -->
        <table class="right-table">
          <tr>
            <td class="label-cell">UANG SEBESAR</td>
           
            <td>: {{ money_id($receipt->total_amount) }} -</td>
          </tr>
          <tr>
            <td></td>
            <td class="content-cell">( {{ terbilang($receipt->total_amount) }} rupiah )</td>
          </tr>
        </table>

        <div class="mt-6"></div>

        <!-- Y A I T U -->
        <table class="right-table">
          <tr>
            <td class="label-cell">Y A I T U</td>
            <td class="content-cell">: Pembayaran Biaya Perjalanan Dinas ke {{ $kota ?? '-' }} An. {{ $receipt->payeeUser->fullNameWithTitles() ?? '-' }}  @if($maksud){{ $maksud }}  @endif</td>
          </tr>
         
        </table>

        <div class="mt-6"></div>

        <!-- Tanggal dan Nomor -->
        <table class="right-table">
          <tr>
            <td class="label-cell">Tanggal</td>
            <td class="content-cell">: {{ $bulanTahun }}</td>
          </tr>
          <tr>
            <td class="label-cell">Nomor</td>
            <td class="content-cell">: {{ $receipt->sppd->doc_no ?? '-' }}</td>
          </tr>
        </table>

        <div class="mt-6"></div>

        <!-- Yang terima -->
        <div class="text-center">Yang terima</div>
        <div class="sign-place"></div>
        <div class="text-center name">{{ $receipt->payeeUser->fullNameWithTitles() ?? '-' }}</div>
        <div class="text-center muted">NIP. {{ $receipt->payeeUser->nip ?? '-' }}</div>
      </td>
    </tr>
  </table>

  <!-- === BAGIAN 2: PERHITUNGAN SPPD RAMPUNG (tabel terpisah) === -->
  <div class="mt-6 text-center" style="font-weight:bold;">PERHITUNGAN SPPD RAMPUNG</div>
  <table class="table">
    <thead>
      <tr>
        <th style="width:15mm;">No</th>
        <th>Uraian</th>
        <th style="width:25mm;">Uang Muka</th>
        <th style="width:30mm;">Jumlah Ditetapkan</th>
        <th style="width:30mm;">Lebih (Kurang)</th>
      </tr>
    </thead>
    <tbody>
      @php
        $categoryNames = [
          'transport' => 'Transportasi',
          'lodging' => 'Penginapan',
          'per_diem' => 'Uang Harian',
          'representation' => 'Representatif',
          'other' => 'Biaya Lainnya'
        ];
        
        $allCategories = ['transport', 'lodging', 'per_diem', 'representation', 'other'];
        $existingCategories = $lines->pluck('category')->unique()->toArray();
      @endphp

      @foreach($allCategories as $index => $category)
        @php
          $categoryLines = $lines->where('category', $category);
          $hasData = $categoryLines->count() > 0;
        @endphp

        <!-- Header Kategori -->
        <tr>
          <td class="text-center">{{ $index + 1 }}.</td>
          <td>{{ $categoryNames[$category] ?? ucfirst($category) }}</td>
          <td class="text-right"></td>
          <td class="text-right"></td>
          <td class="text-right"></td>
        </tr>

        @if($hasData)
          <!-- Detail Items untuk kategori ini -->
          @foreach($categoryLines as $line)
          <tr>
            <td></td>
            <td style="padding-left: 20px;">
              - 
              @switch($line->component)
                @case('AIRFARE')
                  Tiket Pesawat ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }})
                  @break
                @case('INTRA_PROV')
                  Transport Dalam Provinsi ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }})
                  {{-- {{ $line->desc ? ' - ' . $line->desc : '' }} --}}
                  @break
                @case('INTRA_DISTRICT')
                  Transport Dalam Kabupaten ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }})
                  {{-- {{ $line->desc ? ' - ' . $line->desc : '' }} --}}
                  @break
                @case('OFFICIAL_VEHICLE')
                  Kendaraan Dinas ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }})
                  {{-- {{ $line->desc ? ' - ' . $line->desc : '' }} --}}
                  @break
                @case('TAXI')
                  Taxi ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }})
                  {{-- {{ $line->desc ? ' - ' . $line->desc : '' }} --}}
                  @break
                @case('RORO')
                  Kapal RORO ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }})
                  {{-- {{ $line->desc ? ' - ' . $line->desc : '' }} --}}
                  @break
                @case('TOLL')
                  Tol ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }})
                  {{-- {{ $line->desc ? ' - ' . $line->desc : '' }} --}}
                  @break
                @case('PARKIR_INAP')
                  Parkir & Penginapan ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }})
                  {{-- {{ $line->desc ? ' - ' . $line->desc : '' }} --}}
                  @break
                @case('LODGING')
                  @php
                    $isNoLodging = isNoLodging($line, $receipt);
                    $referenceRate = getReferenceRate($line, $receipt);
                    // Debug: uncomment untuk melihat nilai
                    // echo "<!-- Debug LODGING: unit_amount=" . $line->unit_amount . ", referenceRate=" . $referenceRate . ", isNoLodging=" . ($isNoLodging ? 'true' : 'false') . " -->";
                    // if ($referenceRate) {
                    //   $expectedAmount = $referenceRate * 0.3;
                    //   echo "<!-- Debug: expectedAmount=" . $expectedAmount . ", difference=" . abs($line->unit_amount - $expectedAmount) . " -->";
                    // }
                  @endphp
                  Penginapan
                  @if($isNoLodging && $referenceRate)
                    ({{ number_format($line->qty, 0, ',', '.') }} Malam x (30% x {{ money_id($referenceRate) }}))
                  @else
                    ({{ number_format($line->qty, 0, ',', '.') }} Malam x {{ money_id($line->unit_amount) }})
                  @endif
                  {{-- {{ $line->desc ? ' - ' . $line->desc : '' }} --}}
                  @break
                @case('PERDIEM')
                  ({{ number_format($line->qty, 0, ',', '.') }} hari x {{ money_id($line->unit_amount) }})
                  @break
                @case('REPRESENTASI')
                  Biaya representatif ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }})
                  @break
                @case('LAINNYA')
                  {{ $line->remark ?: 'Biaya tambahan' }} ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }})
                  {{-- {{ $line->desc ? ' - ' . $line->desc : '' }} --}}
                  @break
                @default
                  {{ ucfirst(str_replace('_', ' ', $line->component)) }} ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }})
              @endswitch
            </td>
            <td class="text-right">-</td>
            <td class="text-right">{{ money_id($line->line_total) }}</td>
            <td class="text-right">-</td>
          </tr>
          @endforeach
        @else
          <!-- Jika tidak ada data untuk kategori ini -->
          <tr>
            <td></td>
            <td style="padding-left: 20px;">- Tidak ada data</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
          </tr>
        @endif
      @endforeach

      <tr>
        <td class="text-center" colspan="2" style="font-weight:bold;">JUMLAH</td>
        <td class="text-right">-</td>
        <td class="text-right" style="font-weight:bold;">{{ money_id($receipt->total_amount) }}</td>
        <td class="text-right">-</td>
      </tr>
    </tbody>
  </table>

  <!-- TTD bawah menggunakan tabel 2 kolom -->
  <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
    <tr>
      <!-- Kolom 1: Mengetahui -->
      <td style="width: 50%; text-align: center; vertical-align: bottom; padding: 8px;">
        <div>Mengetahui</div>
        <div class="muted">Pejabat Pelaksana Teknis Kegiatan</div>
        <div class="sign-place"></div>
        @php($pptk = $receipt->sppd->getPptkSnapshotFromSubKegiatan())
        <div class="name">{{ ($pptk['name'] ?? null) ?: '-' }}</div>
        <div class="muted">NIP. {{ ($pptk['nip'] ?? null) ?: '-' }}</div>
      </td>
      
      <!-- Kolom 2: Dihitung Oleh -->
      <td style="width: 50%; text-align: center; vertical-align: bottom; padding: 8px;">
        <div>Bengkalis, {{ $receipt->receipt_date ? \Carbon\Carbon::parse($receipt->receipt_date)->locale('id')->translatedFormat('d F Y') : '__________' }}</div>
        <div>Dihitung Oleh :</div>
        <div class="muted">{{ $receipt->treasurer_title ?? 'Bendahara Pengeluaran Pembantu' }}</div>
        <div class="sign-place"></div>
        <div class="name">{{ $receipt->getTreasurerUserSnapshot()['name'] ?? '-' }}</div>
        <div class="muted">NIP. {{ $receipt->getTreasurerUserSnapshot()['nip'] ?? '-' }}</div>
      </td>
    </tr>
  </table>
</body>
</html>


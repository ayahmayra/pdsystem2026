<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Pegawai - {{ $monthName }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 15px;
            background-color: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }
        
        .table-container {
            width: 100%;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            table-layout: fixed;
            font-size: 8px;
        }
        
        /* Lebar kolom */
        table th:nth-child(1), table td:nth-child(1) { width: 4%; }
        table th:nth-child(2), table td:nth-child(2) { width: 20%; }
        table th:nth-child(3), table td:nth-child(3) { width: 20%; }
        table th:nth-child(n+4), table td:nth-child(n+4) { width: 3%; }
        
        th, td {
            border: 1px solid #000000;
            padding: 3px 4px;
            text-align: center;
            vertical-align: middle;
            font-size: 8px;
            line-height: 1.3;
        }
        
        th {
            background-color: #f9fafb;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Warna background */
        .assignment-bg { background-color: #dbeafe !important; } /* biru */
        .weekend-bg { background-color: #fecaca !important; } /* merah */
        
        /* Indicator "D" */
        .assignment-indicator {
            font-weight: bold;
            font-size: 10px;
            color: #1d4ed8;
            margin: 0 auto;
            display: block;
        }
        
        /* Teks nama & info */
        .name-info { line-height: 1.4; }
        .name-info .full-name { font-weight: bold; font-size: 9px; }
        .name-info .nip,
        .name-info .rank {
            font-size: 8px;
            margin: 1px 0;
            line-height: 1.3;
            color: #444;
        }
        
        /* Legend */
        .legend {
            margin-top: 20px;
            font-size: 10px;
            text-align: left;
        }
        
        .legend-item {
            display: inline-block;
            margin-right: 20px;
        }
        
        .legend-color {
            display: inline-block;
            width: 14px;
            height: 14px;
            margin-right: 6px;
            border: 1px solid #000;
            vertical-align: middle;
        }
        
        /* Footer */
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAPITULASI JADWAL PERJALANAN DINAS PEGAWAI</h1>
        <p>Periode: {{ $monthName }}</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th style="text-align: left;">NAMA LENGKAP, NIP, PANGKAT</th>
                    <th style="text-align: left;">JABATAN / UNIT</th>
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        <th>{{ $day }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($pegawai as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align: left;">
                            <div class="name-info">
                                <div class="full-name">{{ $p->fullNameWithTitles() }}</div>
                                <div class="nip">NIP: {{ $p->nip ?? '-' }}</div>
                                <div class="rank">{{ $p->rank->name ?? '-' }}</div>
                            </div>
                        </td>
                        <td style="text-align: left;">
                            <div style="line-height: 1.3;">
                                <div style="font-weight: bold; margin-bottom: 2px;">{{ $p->position->name ?? '-' }}</div>
                                <div style="font-size: 7px; color: #666;">{{ $p->unit->name ?? '-' }}</div>
                            </div>
                        </td>
                        
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $currentDate = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, $day)->startOfDay();
                                $hasAssignment = false;
                                $isWeekend = $currentDate->isWeekend();

                                if (isset($scheduleData[$p->id])) {
                                    foreach ($scheduleData[$p->id] as $assignment) {
                                        if ($currentDate->gte($assignment['start_date']) && $currentDate->lte($assignment['end_date'])) {
                                            $hasAssignment = true;
                                            break;
                                        }
                                    }
                                }

                                $cellClass = '';
                                if ($hasAssignment) {
                                    $cellClass = 'assignment-bg';
                                } elseif ($isWeekend) {
                                    $cellClass = 'weekend-bg';
                                }
                            @endphp
                            
                            <td class="{{ $cellClass }}">
                                @if($hasAssignment)
                                    <div class="assignment-indicator">D</div>
                                @endif
                            </td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="legend">
        <div class="legend-item">
            <span class="legend-color assignment-bg"></span> Perjalanan Dinas
        </div>
        <div class="legend-item">
            <span class="legend-color weekend-bg"></span> Hari Libur (Sabtu/Minggu)
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari sistem Perjalanan Dinas</p>
    </div>
</body>
</html>

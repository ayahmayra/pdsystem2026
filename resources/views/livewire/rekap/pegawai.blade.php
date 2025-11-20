<div>
    <div >
        <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Rekapitulasi Pegawai</h1>
        </div>



        <!-- Filters -->
        <div class="mt-6 bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Filter Data</h3>
                <div class="flex flex-wrap gap-4 items-end">
                    <div class="flex-shrink-0 w-48">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pencarian</label>
                        <input type="text" wire:model.live="search" id="search" placeholder="Cari nama, NIP, atau email..." class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>

                    <div class="flex-shrink-0 w-40">
                        <label for="unit_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit/Bidang</label>
                        <select wire:model.live="unit_filter" id="unit_filter" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Semua Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-shrink-0 w-40">
                        <label for="position_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan</label>
                        <select wire:model.live="position_filter" id="position_filter" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Semua Jabatan</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-shrink-0 w-40">
                        <label for="rank_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pangkat</label>
                        <select wire:model.live="rank_filter" id="rank_filter" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Semua Pangkat</option>
                            @foreach($ranks as $rank)
                                <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-shrink-0 w-32">
                        <label for="selected_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bulan</label>
                        <select wire:model.live="selected_month" id="selected_month" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>

                    <div class="flex-shrink-0 w-24">
                        <label for="selected_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun</label>
                        <select wire:model.live="selected_year" id="selected_year" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @for($year = date('Y') - 2; $year <= date('Y') + 1; $year++)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Schedule Table -->
        <div class="mt-6 bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        Jadwal Perjalanan Dinas - {{ $monthName }}
                    </h3>
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Total: {{ $pegawai->total() }} pegawai
                        </div>
                        <a href="{{ route('rekap.pegawai.pdf', [
                            'search' => $search,
                            'unit_filter' => $unit_filter,
                            'position_filter' => $position_filter,
                            'rank_filter' => $rank_filter,
                            'selected_month' => $selectedMonth,
                            'selected_year' => $selectedYear
                        ]) }}" 
                           target="_blank"
                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Cetak PDF
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto overflow-y-hidden">
                    <table class="min-w-full border-collapse border-2 border-black">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700 border-2 border-black">No</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700 w-80 border-2 border-black">Nama Lengkap, NIP, Pangkat</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700 border-2 border-black">Jabatan</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700 border-2 border-black">Unit</th>
                                
                                <!-- Date columns -->
                                @for($day = 1; $day <= $daysInMonth; $day++)
                                    <th class="px-0.5 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-8 border-2 border-black">
                                        {{ $day }}
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800">
                            @forelse($pegawai as $index => $p)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 border-2 border-black">
                                        {{ $pegawai->firstItem() + $index }}
                                    </td>
                                    <td class="px-2 py-2 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 w-80 border-2 border-black">
                                        <div class="whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $p->fullNameWithTitles() }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                                                NIP: {{ $p->nip ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $p->rank->name ?? '-' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 border-2 border-black">
                                        {{ $p->position->name ?? '-' }}
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 border-2 border-black">
                                        {{ $p->unit->name ?? '-' }}
                                    </td>
                                    
                                    <!-- Date cells -->
                                    @for($day = 1; $day <= $daysInMonth; $day++)
                                        @php
                                            $currentDate = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, $day)->startOfDay();
                                            $hasAssignment = false;
                                            $assignmentInfo = null;
                                            $isWeekend = $currentDate->isWeekend(); // Check if Saturday or Sunday
                                            
                                            if (isset($scheduleData[$p->id])) {
                                                foreach ($scheduleData[$p->id] as $assignment) {
                                                    // Check if current date is within the trip date range (inclusive)
                                                    $startDate = $assignment['start_date'];
                                                    $endDate = $assignment['end_date'];
                                                    
                                                    // Debug: Log the comparison
                                                    // \Log::info("Comparing: Current={$currentDate->format('Y-m-d')}, Start={$startDate->format('Y-m-d')}, End={$endDate->format('Y-m-d')}, GTE=" . ($currentDate->gte($startDate) ? 'true' : 'false') . ", LTE=" . ($currentDate->lte($endDate) ? 'true' : 'false'));
                                                    
                                                    if ($currentDate->gte($startDate) && $currentDate->lte($endDate)) {
                                                        $hasAssignment = true;
                                                        $assignmentInfo = $assignment;
                                                        break;
                                                    }
                                                }
                                            }
                                            
                                            // Determine background color
                                            $bgClass = '';
                                            if ($hasAssignment) {
                                                $bgClass = 'bg-blue-200 dark:bg-blue-800';
                                            } elseif ($isWeekend) {
                                                $bgClass = 'bg-red-100 dark:bg-red-900';
                                            }
                                        @endphp
                                        
                                        <td class="px-0.5 py-0.5 text-center text-xs border-2 border-black {{ $bgClass }} relative">
                                            @if($hasAssignment)
                                                <div class="tooltip-container relative w-full h-full">
                                                    <div class="w-3 h-3 bg-blue-700 dark:bg-blue-300 rounded-full mx-auto shadow-sm"></div>
                                                    <div class="tooltip absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-black text-white text-xs rounded-lg shadow-xl opacity-0 pointer-events-none whitespace-nowrap z-20 min-w-max border-2 border-white">
                                                        <div class="font-semibold text-blue-300 mb-1 bg-gray-800 px-2 py-1 rounded">{{ $assignmentInfo['doc_no'] }}</div>
                                                        <div class="mb-1 bg-gray-800 px-2 py-1 rounded">
                                                            <span class="text-yellow-300">Dari:</span> {{ $assignmentInfo['origin_place'] }}
                                                        </div>
                                                        <div class="mb-1 bg-gray-800 px-2 py-1 rounded">
                                                            <span class="text-green-300">Ke:</span> {{ $assignmentInfo['destination_city'] }}
                                                        </div>
                                                        <div class="text-xs text-gray-300 border-t border-gray-600 pt-1 bg-gray-800 px-2 py-1 rounded">
                                                            {{ $assignmentInfo['start_date']->format('d/m/Y') }} - {{ $assignmentInfo['end_date']->format('d/m/Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 4 + $daysInMonth }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                        Tidak ada data pegawai
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $pegawai->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.tooltip-container:hover .tooltip {
    opacity: 1 !important;
    transition: opacity 0.2s ease-in-out;
    background-color: #000000 !important;
    border: 2px solid #ffffff !important;
}

/* Ensure table borders are visible */
table {
    border-collapse: collapse !important;
}

th, td {
    border: 2px solid #000000 !important;
}

/* Override any conflicting styles */
.border-2 {
    border-width: 2px !important;
}

.border-black {
    border-color: #000000 !important;
}
</style>
</div>

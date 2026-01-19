<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Buat Kwitansi
                    </h2>
                    <a 
                        href="{{ $this->getBackUrl() }}" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                    >
                        Kembali
                    </a>
                </div>

                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- SPPD Selection (if spt_id is provided) -->
                @if($spt && $availableSppds->count() > 0 && !$sppd)
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pilih SPPD</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Pilih SPPD yang akan dibuatkan kwitansi:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($availableSppds as $availableSppd)
                                @php
                                    // Get participants who already have receipts for this SPPD
                                    $participantsWithReceipts = \App\Models\Receipt::where('sppd_id', $availableSppd->id)
                                        ->pluck('payee_user_id')
                                        ->toArray();
                                    
                                    // Get available participants and sort them
                                    $availableParticipants = $availableSppd->spt->notaDinas->participants->filter(function ($participant) use ($participantsWithReceipts) {
                                        return !in_array($participant->user_id, $participantsWithReceipts);
                                    })->sort(function ($a, $b) {
                                        // 1. Sort by eselon (position_echelon_id) - lower number = higher eselon
                                        $ea = $a->user_position_echelon_id_snapshot ?? $a->user?->position?->echelon?->id ?? 999999;
                                        $eb = $b->user_position_echelon_id_snapshot ?? $b->user?->position?->echelon?->id ?? 999999;
                                        if ($ea !== $eb) return $ea <=> $eb;
                                        
                                        // 2. Sort by rank (rank_id) - higher number = higher rank
                                        $ra = $a->user_rank_id_snapshot ?? $a->user?->rank?->id ?? 0;
                                        $rb = $b->user_rank_id_snapshot ?? $b->user?->rank?->id ?? 0;
                                        if ($ra !== $rb) return $rb <=> $ra; // DESC order for rank
                                        
                                        // 3. Sort by NIP (alphabetical)
                                        $na = (string)($a->user_nip_snapshot ?? $a->user?->nip ?? '');
                                        $nb = (string)($b->user_nip_snapshot ?? $b->user?->nip ?? '');
                                        return strcmp($na, $nb);
                                    })->values();
                                @endphp
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                                     wire:click="selectSppd({{ $availableSppd->id }})">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $availableSppd->doc_no }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ count($availableParticipants) > 0 ? $availableParticipants[0]['user_name_snapshot'] : 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                        NIP: {{ count($availableParticipants) > 0 ? $availableParticipants[0]['user_nip_snapshot'] : 'N/A' }}
                                    </div>
                                    <div class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                                        {{ count($availableParticipants) }} peserta tersedia untuk kwitansi
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Form (only show if SPPD is selected) -->
                @if($sppd)
                    <form wire:submit="save">
                        <!-- Informasi Nota Dinas dan SPT sebagai Referensi -->
                        @if($sppd->spt && $sppd->spt->notaDinas)
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Informasi Nota Dinas & SPT (Referensi)
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                <!-- Nota Dinas Info -->
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Nomor Nota Dinas:</span>
                                    <p class="text-gray-900 dark:text-white font-mono">{{ $sppd->spt->notaDinas->doc_no }}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Tanggal Nota Dinas:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $sppd->spt->notaDinas->nd_date ? \Carbon\Carbon::parse($sppd->spt->notaDinas->nd_date)->locale('id')->translatedFormat('d F Y') : '-' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Bidang Pengaju:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $sppd->spt->notaDinas->requestingUnit->name ?? '-' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Dari:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $sppd->spt->notaDinas->fromUser->fullNameWithTitles() ?? '-' }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $sppd->spt->notaDinas->fromUser->position->name ?? '-' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Kepada:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $sppd->spt->notaDinas->toUser->fullNameWithTitles() ?? '-' }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $sppd->spt->notaDinas->toUser->position->name ?? '-' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Tujuan:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $sppd->spt->notaDinas->destinationCity->name ?? '-' }}, {{ $sppd->spt->notaDinas->destinationCity->province->name ?? '-' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Periode Perjalanan:</span>
                                    <p class="text-gray-900 dark:text-white">
                                        {{ $sppd->spt->notaDinas->start_date ? \Carbon\Carbon::parse($sppd->spt->notaDinas->start_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                        s.d
                                        {{ $sppd->spt->notaDinas->end_date ? \Carbon\Carbon::parse($sppd->spt->notaDinas->end_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        ({{ $sppd->spt->notaDinas->start_date && $sppd->spt->notaDinas->end_date ? \Carbon\Carbon::parse($sppd->spt->notaDinas->start_date)->diffInDays(\Carbon\Carbon::parse($sppd->spt->notaDinas->end_date)) + 1 : 0 }} hari)
                                    </p>
                                </div>
                                <div class="space-y-1 md:col-span-2 lg:col-span-3">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Hal:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $sppd->spt->notaDinas->hal }}</p>
                                </div>
                                
                                <!-- SPT Info -->
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Nomor SPT:</span>
                                    <p class="text-gray-900 dark:text-white font-mono">{{ $sppd->spt->doc_no }}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Tanggal SPT:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $sppd->spt->spt_date ? \Carbon\Carbon::parse($sppd->spt->spt_date)->locale('id')->translatedFormat('d F Y') : '-' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Penandatangan SPT:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $sppd->spt->signedByUser->fullNameWithTitles() ?? '-' }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $sppd->spt->signedByUser->position->name ?? '-' }}</p>
                                </div>
                                
                                @if($sppd->spt->notaDinas->participants && $sppd->spt->notaDinas->participants->count() > 0)
                                <div class="space-y-1 md:col-span-2 lg:col-span-3">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Peserta Perjalanan:</span>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        @foreach($sppd->spt->notaDinas->getSortedParticipants() as $participant)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $participant->user->fullNameWithTitles() }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Auto-fill Information -->
                        @php
                            $existingReceipt = \App\Models\Receipt::where('sppd_id', $sppd->id)->first();
                        @endphp
                        @if($existingReceipt)
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Auto-fill dari Kwitansi Sebelumnya
                            </h3>
                            <p class="text-sm text-green-700 dark:text-green-300 mb-3">
                                Beberapa field telah diisi otomatis berdasarkan kwitansi pertama yang sudah dibuat untuk SPPD ini. 
                                Anda dapat mengubah nilai-nilai ini jika diperlukan.
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                @if($existingReceipt->account_code)
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Kode Rekening:</span>
                                    <p class="text-green-700 dark:text-green-300 font-mono">{{ $existingReceipt->account_code }}</p>
                                </div>
                                @endif
                                @if($existingReceipt->treasurer_user_id)
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Bendahara:</span>
                                    <p class="text-green-700 dark:text-green-300">{{ $existingReceipt->treasurerUser->fullNameWithTitles() ?? 'N/A' }}</p>
                                </div>
                                @endif
                                @if($existingReceipt->treasurer_title)
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Titel Bendahara:</span>
                                    <p class="text-green-700 dark:text-green-300">{{ $existingReceipt->treasurer_title }}</p>
                                </div>
                                @endif
                                @if($existingReceipt->receipt_date)
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Tanggal Kwitansi:</span>
                                    <p class="text-green-700 dark:text-green-300">{{ \Carbon\Carbon::parse($existingReceipt->receipt_date)->format('d/m/Y') }}</p>
                                </div>
                                @endif
                                @if($existingReceipt->travel_grade_id)
                                <div class="space-y-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Tingkat Perjalanan Dinas:</span>
                                    <p class="text-green-700 dark:text-green-300">{{ $existingReceipt->travelGrade->name ?? 'N/A' }} ({{ $existingReceipt->travelGrade->code ?? 'N/A' }})</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- SPPD Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi SPPD</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Nomor SPPD
                                    </label>
                                    <div class="text-sm text-gray-900 dark:text-white font-medium">
                                        {{ $sppd->doc_no }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Tanggal SPPD
                                    </label>
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $sppd->sppd_date ? \Carbon\Carbon::parse($sppd->sppd_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Penandatangan SPPD
                                    </label>
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $sppd->signedByUser->fullNameWithTitles() ?? '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $sppd->signedByUser->position->name ?? '-' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        PPTK
                                    </label>
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $sppd->subKeg?->pptkUser?->fullNameWithTitles() ?? '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $sppd->subKeg?->pptkUser?->position?->name ?? '-' }}
                                    </div>
                                </div>
                               
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <div class="space-y-6">

                                <!-- Peserta (Penerima Pembayaran) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Peserta (Penerima Pembayaran) <span class="text-red-500">*</span>
                                    </label>
                                    @if(count($availableParticipants) > 0)
                                        <select 
                                            wire:model="payee_user_id" 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        >
                                            <option value="">Pilih Peserta</option>
                                            @foreach($availableParticipants as $participant)
                                                <option value="{{ $participant['user_id'] }}">
                                                    {{ $participant['user_name_snapshot'] ?? 'N/A' }} 
                                                    ({{ $participant['user_position_name_snapshot'] ?? 'N/A' }} - {{ $participant['user_rank_name_snapshot'] ?? 'N/A' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Hanya menampilkan peserta yang belum memiliki kwitansi untuk SPPD ini.
                                        </div>
                                    @else
                                        <div class="px-3 py-2 border border-gray-300 rounded-md bg-gray-100 dark:bg-gray-700 dark:border-gray-600 text-gray-500 dark:text-gray-400">
                                            Tidak ada peserta yang tersedia untuk dibuatkan kwitansi.
                                        </div>
                                    @endif
                                    @error('payee_user_id') 
                                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Rekening Belanja -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Rekening Belanja <span class="text-red-500">*</span>
                                    </label>
                                    
                                    
                                    @if(count($availableRekeningBelanja) > 0)
                                        <flux:select wire:model="rekening_belanja_id" variant="listbox" searchable placeholder="Pilih Rekening Belanja...">
                                            <flux:select.option value="">Pilih Rekening Belanja</flux:select.option>
                                            @foreach($availableRekeningBelanja as $rekening)
                                                <flux:select.option value="{{ $rekening->id }}">
                                                    {{ $rekening->kode_rekening }} - {{ $rekening->nama_rekening }}
                                                    @if($rekening->pagu > 0)
                                                        (Pagu: Rp {{ number_format($rekening->pagu, 0, ',', '.') }})
                                                    @endif
                                                    @if($rekening->total_realisasi > 0)
                                                        | Realisasi: Rp {{ number_format($rekening->total_realisasi, 0, ',', '.') }}
                                                    @endif
                                                    @if($rekening->sisa_anggaran != 0)
                                                        | Sisa: {{ $rekening->sisa_anggaran > 0 ? 'Rp ' . number_format($rekening->sisa_anggaran, 0, ',', '.') : '-Rp ' . number_format(abs($rekening->sisa_anggaran), 0, ',', '.') }}
                                                    @endif
                                                </flux:select.option>
                                            @endforeach
                                        </flux:select>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Rekening belanja berdasarkan sub kegiatan dari SPPD terkait.
                                        </div>
                                    @else
                                        <div class="px-3 py-2 border border-gray-300 rounded-md bg-yellow-100 dark:bg-yellow-900/20 border-yellow-300 dark:border-yellow-700 text-yellow-800 dark:text-yellow-200">
                                            @if($sppd && !$sppd->sub_keg_id)
                                                <strong>Perhatian:</strong> SPPD ini belum memiliki sub kegiatan yang dipilih. Silakan edit SPPD terlebih dahulu untuk memilih sub kegiatan.
                                            @elseif($sppd && $sppd->subKeg && $sppd->subKeg->activeRekeningBelanja->count() == 0)
                                                <strong>Perhatian:</strong> Sub kegiatan "{{ $sppd->subKeg->nama_subkeg }}" belum memiliki rekening belanja yang aktif. Silakan tambahkan rekening belanja untuk sub kegiatan ini.
                                            @else
                                                Tidak ada rekening belanja yang tersedia untuk sub kegiatan ini.
                                            @endif
                                        </div>
                                    @endif
                                    @error('rekening_belanja_id') 
                                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Tingkat Perjalanan Dinas -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Tingkat Perjalanan Dinas <span class="text-red-500">*</span>
                                    </label>
                                    @php
                                        $travelGrades = \App\Models\TravelGrade::orderBy('name')->get();
                                    @endphp
                                    <select 
                                        wire:model="travel_grade_id" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                        <option value="">Pilih Tingkat Perjalanan Dinas</option>
                                        @foreach($travelGrades as $travelGrade)
                                            <option value="{{ $travelGrade->id }}">
                                                {{ $travelGrade->name }} ({{ $travelGrade->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        @if($payee_user_id)
                                            @if($hasTravelGradeWarning)
                                                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3 mt-2">
                                                    <div class="text-yellow-700 dark:text-yellow-300">
                                                        <strong>⚠️ Peringatan:</strong> {{ $travelGradeWarningMessage }}
                                                    </div>
                                                </div>
                                            @else
                                                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 mt-2">
                                                    <div class="text-green-700 dark:text-green-300">
                                                        <strong>✓ Status:</strong> Tingkat perjalanan dinas peserta sudah ditentukan
                                                    </div>
                                                    <div class="text-green-600 dark:text-green-400 text-xs mt-1">
                                                        📋 Menggunakan data snapshot dari nota dinas
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-gray-500 dark:text-gray-400">
                                                Pilih peserta terlebih dahulu untuk melihat status tingkat perjalanan dinas
                                            </div>
                                        @endif
                                    </div>
                                    @error('travel_grade_id') 
                                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                                    @enderror
                                </div>



                                <!-- Nama Bendahara -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Nama Bendahara <span class="text-red-500">*</span>
                                    </label>
                                    <div x-data="searchableSelect({
                                        options: {{ Js::from(\App\Models\User::orderBy('name')->get()->map(function($user) {
                                            return [
                                                'id' => $user->id,
                                                'text' => $user->fullNameWithTitles() . ' (' . trim(($user->position?->name ?? '') . ' ' . ($user->unit?->name ?? '')) . ')',
                                                'name' => $user->name,
                                                'nip' => $user->nip,
                                                'position' => $user->position?->name,
                                                'unit' => $user->unit?->name
                                            ];
                                        })) }},
                                        selectedValue: @entangle('treasurer_user_id'),
                                        placeholder: 'Cari dan pilih bendahara...'
                                    })">
                                        <!-- Search Input -->
                                        <div class="relative mt-1">
                                            <input 
                                                type="text" 
                                                x-ref="searchInput"
                                                x-model="searchTerm"
                                                @click="open = true"
                                                @keydown.escape="open = false"
                                                @keydown.arrow-down.prevent="selectNext()"
                                                @keydown.arrow-up.prevent="selectPrevious()"
                                                @keydown.enter.prevent="selectCurrent()"
                                                placeholder="Cari dan pilih bendahara..."
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                :class="{ 'border-blue-500': open }"
                                            >
                                            
                                            <!-- Dropdown -->
                                            <div 
                                                x-show="open" 
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                x-transition:leave-end="opacity-0 transform scale-95"
                                                @click.away="open = false"
                                                class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto"
                                            >
                                                <template x-for="(option, index) in filteredOptions" :key="option.id">
                                                    <div 
                                                        @click="selectOption(option)"
                                                        class="px-3 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                                                        :class="{ 'bg-blue-100 dark:bg-blue-900': index === selectedIndex }"
                                                    >
                                                        <div class="font-medium" x-text="option.text"></div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400" x-text="'NIP: ' + (option.nip || '-')"></div>
                                                    </div>
                                                </template>
                                                
                                                <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-gray-500 dark:text-gray-400">
                                                    Tidak ada hasil yang ditemukan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @error('treasurer_user_id') 
                                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Titel Bendahara -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Titel Bendahara <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        wire:model="treasurer_title" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                        <option value="">Pilih Titel</option>
                                        <option value="Bendahara Pengeluaran">Bendahara Pengeluaran</option>
                                        <option value="Bendahara Pengeluaran Pembantu">Bendahara Pengeluaran Pembantu</option>
                                    </select>
                                    @error('treasurer_title') 
                                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Tanggal Kwitansi -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Tanggal Kwitansi <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="date" 
                                        wire:model="receipt_date" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    @error('receipt_date') 
                                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Nomor Kwitansi -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Nomor Kwitansi (SIPD)
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="receipt_no" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Contoh: KWT-001/2024 atau nomor dari SIPD"
                                    />
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Nomor kwitansi dapat diisi manual sesuai format yang diinginkan atau nomor dari aplikasi SIPD. Bisa dikosongkan untuk sementara.
                                    </div>
                                    @error('receipt_no') 
                                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>


                                <!-- Reference Rates & Perhitungan Biaya (hanya tampil jika travel grade sudah dipilih) -->
                                @if($travel_grade_id)
                                <div class="border-t border-gray-200 dark:border-gray-600 pt-6 mt-6">
                                    
                                    <!-- Reference Rates Section - Collapsible -->
                                    @if($airfareRate || $lodgingCap || $perdiemDailyRate || $representationRate)
                                    <div x-data="{ openRates: false }" class="mb-6">
                                        <button 
                                            type="button"
                                            @click="openRates = !openRates" 
                                            class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 border border-blue-200 dark:border-gray-500 rounded-lg hover:shadow-md transition-all duration-200"
                                        >
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="font-semibold text-gray-900 dark:text-white">Referensi Tarif Maksimal</span>
                                                <span class="ml-3 text-xs px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full">Panduan</span>
                                            </div>
                                            <svg 
                                                class="w-5 h-5 text-gray-600 dark:text-gray-300 transition-transform duration-200" 
                                                :class="{ 'rotate-180': openRates }"
                                                fill="none" 
                                                stroke="currentColor" 
                                                viewBox="0 0 24 24"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        
                                        <div 
                                            x-show="openRates" 
                                            x-collapse
                                            class="mt-3 p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg"
                                        >
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                                Berikut adalah tarif referensi maksimal berdasarkan peraturan perjalanan dinas. Tarif ini akan otomatis terisi pada form di bawah.
                                            </p>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                                @if($airfareRate)
                                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Tiket Pesawat</span>
                                                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400 font-mono mt-1">
                                                        Rp {{ number_format($airfareRate, 0, ',', '.') }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ $airfareOrigin }} → {{ $airfareDestination }}
                                                    </p>
                                                </div>
                                                @endif
                                                
                                                @if($lodgingCap)
                                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Maksimal Penginapan /malam</span>
                                                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400 font-mono mt-1">
                                                        Rp {{ number_format($lodgingCap, 0, ',', '.') }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        Provinsi {{ $lodgingProvince }}
                                                    </p>
                                                </div>
                                                @endif
                                                
                                                @if($perdiemDailyRate)
                                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Uang Harian /hari</span>
                                                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400 font-mono mt-1">
                                                        Rp {{ number_format($perdiemDailyRate, 0, ',', '.') }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ ucfirst(str_replace('_', ' ', $perdiemTripType)) }} - {{ $perdiemProvince }}
                                                    </p>
                                                </div>
                                                @endif
                                                
                                                @if($perdiemTotalAmount)
                                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Total Uang Harian</span>
                                                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400 font-mono mt-1">
                                                        Rp {{ number_format($perdiemTotalAmount, 0, ',', '.') }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ $this->calculateTripDays($sppd->spt->notaDinas) }} hari
                                                    </p>
                                                </div>
                                                @endif
                                                
                                                @if($representationRate)
                                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Tarif Representasi</span>
                                                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400 font-mono mt-1">
                                                        Rp {{ number_format($representationRate, 0, ',', '.') }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ ucfirst(str_replace('_', ' ', $representationTripType)) }}
                                                    </p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <!-- Main Header with Total Summary -->
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                                            <svg class="w-6 h-6 mr-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            Rincian Biaya
                                        </h3>
                                        
                                        <!-- Sticky Total Card -->
                                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-600 border-2 border-green-500 dark:border-green-600 rounded-lg px-6 py-3 shadow-md">
                                            <div class="text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">TOTAL KWITANSI</div>
                                            <div class="text-2xl font-bold text-green-700 dark:text-green-400 font-mono">
                                                Rp {{ number_format($totalAmount, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Komponen Biaya -->
                                    <div class="space-y-6">
                                        <!-- 1. Biaya Transportasi -->
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-red-500 dark:border-red-400 shadow-sm hover:shadow-md transition-shadow duration-200">
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                        </svg>
                                                    </div>
                                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">1. Biaya Transportasi</h4>
                                                </div>
                                                <button type="button" wire:click="addTransportLine" class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow transition-all duration-200">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                    Tambah Item
                                                </button>
                                            </div>

                                    <!-- Info Alert -->
                                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-3 mb-4">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-500 dark:text-blue-400 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                            <div class="text-xs text-blue-800 dark:text-blue-300">
                                                Beberapa jenis transportasi akan otomatis terisi dengan tarif referensi. Anda dapat mengedit manual jika diperlukan.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Warning Banner for Excessive Values -->
                                    @if($hasExcessiveValues)
                                    <div class="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border-2 border-red-300 dark:border-red-600 rounded-lg p-4 mb-4 shadow-sm">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-red-500 dark:text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <h3 class="text-sm font-bold text-red-800 dark:text-red-300 mb-2">
                                                    ⚠️ Peringatan: Nilai Melebihi Referensi
                                                </h3>
                                                <div class="text-sm text-red-700 dark:text-red-300 space-y-1">
                                                    @foreach($excessiveValueDetails as $detail)
                                                    <div class="flex items-start">
                                                        <span class="inline-block w-2 h-2 bg-red-500 rounded-full mt-1.5 mr-2 flex-shrink-0"></span>
                                                        <span>
                                                            <strong>{{ $detail['type'] }}:</strong> 
                                                            Rp {{ number_format($detail['manual_value'], 0, ',', '.') }} 
                                                            <span class="text-red-600 dark:text-red-400">(+{{ $detail['excess_percentage'] }}% dari referensi)</span>
                                                        </span>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                            
                                            @if(count($transportLines) > 0)
                                                <div class="space-y-3">
                                                    @foreach($transportLines as $index => $line)
                                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4 hover:shadow-sm transition-shadow duration-200">
                                                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                                                            <div class="col-span-2">
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis</label>
                                                                <select wire:model.live="transportLines.{{ $index }}.component" class="w-full h-10 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                                    <option value="">Pilih Jenis</option>
                                                                    <option value="AIRFARE">Tiket Pesawat</option>
                                                                    <option value="INTRA_PROV">Transport Dalam Provinsi</option>
                                                                    <option value="INTRA_DISTRICT">Transport Dalam Kabupaten</option>
                                                                    <option value="OFFICIAL_VEHICLE">Kendaraan Dinas</option>
                                                                    <option value="TAXI">Taxi</option>
                                                                    <option value="RORO">Kapal RORO</option>
                                                                    <option value="TOLL">Tol</option>
                                                                    <option value="PARKIR_INAP">Parkir & Penginapan</option>
                                                                </select>
                                                                
                                                                <!-- Rate Info Display -->
                                                                @if($line['rate_info'])
                                                                <div class="mt-1 text-xs {{ $line['has_reference'] ? 'text-green-600 dark:text-green-400' : ($line['is_overridden'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400') }}">
                                                                    @if($line['has_reference'])
                                                                        ✓ {{ $line['rate_info'] }}
                                                                    @elseif($line['is_overridden'])
                                                                        ✏️ {{ $line['rate_info'] }}
                                                                    @else
                                                                        ℹ {{ $line['rate_info'] }}
                                                                    @endif
                                                                </div>
                                                                @endif
                                                            </div>
                                                            <div class="col-span-3">
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan Tambahan</label>
                                                                <input type="text" wire:model="transportLines.{{ $index }}.desc" class="w-full h-10 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Garuda Indonesia">
                                                            </div>
                                                            <div class="col-span-1">
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah</label>
                                                                <input type="number" wire:model="transportLines.{{ $index }}.qty" min="0" step="0.5" class="w-full h-10 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                            </div>
                                                                                                                    <div class="col-span-2">
                                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Harga Satuan
                                                                @if(($line['has_reference'] ?? false) && !($line['is_overridden'] ?? false))
                                                                    <span class="text-green-600 dark:text-green-400 text-xs">✓ Auto</span>
                                                                @endif
                                                                @if($line['is_overridden'] ?? false)
                                                                    <span class="text-blue-600 dark:text-blue-400 text-xs">✏️ Manual</span>
                                                                @endif
                                                            </label>
                                                            <input type="number" 
                                                                wire:model.live="transportLines.{{ $index }}.unit_amount" 
                                                                min="0" 
                                                                class="w-full h-10 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ (($line['has_reference'] ?? false) && !($line['is_overridden'] ?? false)) ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-600' : (($line['is_overridden'] ?? false) ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-600' : '') }}"
                                                                @if(($line['has_reference'] ?? false) && !($line['is_overridden'] ?? false)) readonly @endif
                                                                placeholder="{{ ($line['has_reference'] ?? false) ? 'Otomatis terisi' : 'Masukkan harga satuan' }}">
                                                            
                                                            <!-- Warning for manual values exceeding reference -->
                                                            @if($line['exceeds_reference'])
                                                            <div class="mt-1 p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded text-xs">
                                                                <div class="text-red-700 dark:text-red-300 font-medium">
                                                                    ⚠️ Nilai melebihi tarif referensi!
                                                                </div>
                                                                <div class="text-red-600 dark:text-red-400 mt-1">
                                                                    <span class="block">• Tarif referensi: Rp {{ number_format($line['original_reference_rate'], 0, ',', '.') }}</span>
                                                                    <span class="block">• Kelebihan: Rp {{ number_format($line['excess_amount'], 0, ',', '.') }} ({{ $line['excess_percentage'] }}%)</span>
                                                                    <span class="block">• Saran: Gunakan tarif referensi untuk efisiensi anggaran</span>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                            <div class="col-span-2">
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Total</label>
                                                                <div class="h-10 px-2 py-1 text-sm bg-gray-100 dark:bg-gray-600 rounded font-mono flex items-center">
                                                                    Rp {{ number_format((float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0), 0, ',', '.') }}
                                                                </div>
                                                            </div>
                                                            <div class="col-span-2 flex items-center justify-end space-x-2 h-10">
                                                                @if(($line['has_reference'] ?? false) && !($line['is_overridden'] ?? false))
                                                                    <button type="button" 
                                                                        wire:click="overrideTransportRate({{ $index }})" 
                                                                        class="px-3 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 border border-blue-300 hover:border-blue-500 rounded transition-colors">
                                                                        Edit Manual
                                                                    </button>
                                                                @endif
                                                                <button type="button" wire:click="removeTransportLine({{ $index }})" class="px-3 py-1 text-xs font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 border border-red-300 hover:border-red-500 rounded transition-colors">
                                                                    Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                                                    Belum ada biaya transportasi yang ditambahkan
                                                </div>
                                            @endif
                                        </div>

                                        <!-- 2. Biaya Penginapan -->
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-yellow-500 dark:border-yellow-400 shadow-sm hover:shadow-md transition-shadow duration-200">
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                                        </svg>
                                                    </div>
                                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">2. Biaya Penginapan</h4>
                                                </div>
                                                <button type="button" wire:click="addLodgingLine" class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow transition-all duration-200">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                    Tambah Item
                                                </button>
                                            </div>
                                            
                                            <!-- Reference rate info for lodging -->
                                            @if($lodgingCap)
                                            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-3 mb-4">
                                                <div class="flex items-start">
                                                    <svg class="w-5 h-5 text-amber-500 dark:text-amber-400 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <div class="text-xs text-amber-800 dark:text-amber-300">
                                                        <strong>Batas Maksimal:</strong> Rp {{ number_format($lodgingCap, 0, ',', '.') }} per malam 
                                                        <span class="text-gray-600 dark:text-gray-400">(Provinsi: {{ $lodgingProvince }})</span>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if(count($lodgingLines) > 0)
                                                <div class="space-y-3">
                                                    @foreach($lodgingLines as $index => $line)
                                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4 hover:shadow-sm transition-shadow duration-200">
                                                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                                                            <!-- Kota Tujuan -->
                                                            <div class="lg:col-span-2">
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Kota Tujuan</label>
                                                                <select wire:model.live="lodgingLines.{{ $index }}.destination_city_id" class="w-full h-10 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                                    <option value="">Tujuan Utama</option>
                                                                    @foreach($availableCities as $city)
                                                                        <option value="{{ $city->id }}">{{ $city->name }}, {{ $city->province->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            
                                                            <!-- Jumlah Malam -->
                                                            <div class="lg:col-span-1">
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Malam</label>
                                                                <input type="number" wire:model="lodgingLines.{{ $index }}.qty" min="0" step="0.5" class="w-full h-10 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                            </div>
                                                            
                                                            <!-- Keterangan Tambahan -->
                                                            <div class="lg:col-span-2">
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                                                                <input type="text" wire:model="lodgingLines.{{ $index }}.desc" class="w-full h-10 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Hotel Bintang 4">
                                                            </div>
                                                            
                                                            <!-- Tidak Menginap Checkbox -->
                                                            <div class="lg:col-span-2">
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Opsi</label>
                                                                <div class="flex items-center h-10 px-2 py-1 border border-gray-200 dark:border-gray-600 rounded bg-white dark:bg-gray-800">
                                                                    <input type="checkbox" wire:model.live="lodgingLines.{{ $index }}.no_lodging" class="mr-2">
                                                                    <span class="text-xs">Tidak Menginap (30%)</span>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Tarif per Malam -->
                                                            <div class="lg:col-span-2">
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                    Tarif /malam
                                                                    @if($line['has_reference'])
                                                                        <span class="text-green-600 dark:text-green-400 text-xs">✓</span>
                                                                    @endif
                                                                    @if($line['is_overridden'])
                                                                        <span class="text-blue-600 dark:text-blue-400 text-xs">✏️</span>
                                                                    @endif
                                                                </label>
                                                                <input type="number" 
                                                                    wire:model.live="lodgingLines.{{ $index }}.unit_amount" 
                                                                    min="0" 
                                                                    class="w-full h-10 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $line['has_reference'] ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-600' : ($line['is_overridden'] ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-600' : '') }}"
                                                                    {{ $line['has_reference'] ? 'readonly' : '' }}
                                                                    placeholder="{{ $line['has_reference'] ? 'Auto' : 'Input tarif' }}">
                                                                
                                                                <!-- Rate Info - Compact -->
                                                                @if($line['rate_info'])
                                                                <div class="mt-1 text-xs {{ $line['has_reference'] ? 'text-green-600 dark:text-green-400' : ($line['is_overridden'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400') }}">
                                                                    {{ $line['has_reference'] ? '✓ ' : ($line['is_overridden'] ? '✏️ ' : '') }}{{ Str::limit($line['rate_info'], 25) }}
                                                                </div>
                                                                @endif
                                                                
                                                                <!-- Warning Compact -->
                                                                @if($line['exceeds_reference'])
                                                                <div class="mt-1 px-2 py-1 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded text-xs text-red-700 dark:text-red-300">
                                                                    ⚠️ +{{ $line['excess_percentage'] }}%
                                                                </div>
                                                                @endif
                                                            </div>
                                                            
                                                            <!-- Total -->
                                                            <div class="lg:col-span-2">
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Total</label>
                                                                <div class="h-10 px-2 py-1 text-sm bg-gray-100 dark:bg-gray-600 rounded font-mono flex items-center font-semibold">
                                                                    Rp {{ number_format((float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0), 0, ',', '.') }}
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Actions -->
                                                            <div class="lg:col-span-1 flex items-center lg:items-end lg:justify-end space-x-2 h-10">
                                                                @if($line['has_reference'])
                                                                    <button type="button" 
                                                                        wire:click="overrideLodgingRate({{ $index }})" 
                                                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-medium">
                                                                        Edit
                                                                    </button>
                                                                @endif
                                                                <button type="button" wire:click="removeLodgingLine({{ $index }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium">
                                                                    Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                                                    Belum ada biaya penginapan yang ditambahkan
                                                </div>
                                            @endif
                                        </div>

                                        <!-- 3. Uang Harian (Perdiem) -->
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-green-500 dark:border-green-400 shadow-sm hover:shadow-md transition-shadow duration-200">
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </div>
                                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">3. Uang Harian (Perdiem)</h4>
                                                </div>
                                                <button type="button" wire:click="addPerdiemLine" class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow transition-all duration-200">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                    Tambah Item
                                                </button>
                                            </div>
                                            
                                            <!-- Reference rate warning for perdiem -->
                                            @if($perdiemDailyRate)
                                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 mb-3">
                                                <div class="text-xs text-green-700 dark:text-green-300">
                                                    <strong>💰 Tarif Standar:</strong> Rp {{ number_format($perdiemDailyRate, 0, ',', '.') }} per hari
                                                    <br><span class="text-gray-600 dark:text-gray-400">
                                                        {{ ucfirst(str_replace('_', ' ', $perdiemTripType)) }} - {{ $perdiemProvince }}
                                                    </span>
                                                    @if($perdiemTotalAmount)
                                                    <br><span class="text-green-600 dark:text-green-400 font-medium">
                                                        Total untuk {{ $this->calculateTripDays($sppd->spt->notaDinas) }} hari: Rp {{ number_format($perdiemTotalAmount, 0, ',', '.') }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if(count($perdiemLines) > 0)
                                                <div class="space-y-3">
                                                    @foreach($perdiemLines as $index => $line)
                                                    <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 p-3">
                                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Hari</label>
                                                                <input type="number" wire:model="perdiemLines.{{ $index }}.qty" min="0" step="0.5" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tarif per Hari</label>
                                                                <input type="number" wire:model="perdiemLines.{{ $index }}.unit_amount" min="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Total</label>
                                                                <div class="px-2 py-1 text-sm bg-gray-100 dark:bg-gray-600 rounded font-mono">
                                                                    Rp {{ number_format((float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0), 0, ',', '.') }}
                                                                </div>
                                                            </div>
                                                            <div class="flex items-end">
                                                                <button type="button" wire:click="removePerdiemLine({{ $index }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                                                    Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                                                    Belum ada uang harian yang ditambahkan
                                                </div>
                                            @endif
                                        </div>

                                        <!-- 4. Biaya Representatif -->
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-teal-500 dark:border-teal-400 shadow-sm hover:shadow-md transition-shadow duration-200">
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">4. Biaya Representatif</h4>
                                                </div>
                                                <button type="button" wire:click="addRepresentationLine" class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow transition-all duration-200">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                    Tambah Item
                                                </button>
                                            </div>
                                            
                                            <!-- Reference rate warning for representation -->
                                            @if($representationRate)
                                            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-3 mb-3">
                                                <div class="text-xs text-purple-700 dark:text-purple-300">
                                                    <strong>🎯 Tarif Standar:</strong> Rp {{ number_format($representationRate, 0, ',', '.') }} per unit
                                                    <br><span class="text-gray-600 dark:text-gray-400">
                                                        {{ ucfirst(str_replace('_', ' ', $representationTripType)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if(count($representationLines) > 0)
                                                <div class="space-y-3">
                                                    @foreach($representationLines as $index => $line)
                                                    <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 p-3">
                                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Hari</label>
                                                                <input type="number" wire:model="representationLines.{{ $index }}.qty" min="0" step="0.5" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tarif per Hari</label>
                                                                <input type="number" wire:model="representationLines.{{ $index }}.unit_amount" min="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Total</label>
                                                                <div class="px-2 py-1 text-sm bg-gray-100 dark:bg-gray-600 rounded font-mono">
                                                                    Rp {{ number_format((float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0), 0, ',', '.') }}
                                                                </div>
                                                            </div>
                                                            <div class="flex items-end">
                                                                <button type="button" wire:click="removeRepresentationLine({{ $index }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                                                    Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                                                    Belum ada biaya representatif yang ditambahkan
                                                </div>
                                            @endif
                                        </div>

                                        <!-- 5. Biaya Lainnya -->
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-indigo-500 dark:border-indigo-400 shadow-sm hover:shadow-md transition-shadow duration-200">
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">5. Biaya Lainnya</h4>
                                                </div>
                                                <button type="button" wire:click="addOtherLine" class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow transition-all duration-200">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                    Tambah Item
                                                </button>
                                            </div>
                                            
                                            @if(count($otherLines) > 0)
                                                <div class="space-y-3">
                                                    @foreach($otherLines as $index => $line)
                                                    <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 p-3">
                                                        <div class="grid grid-cols-12 gap-3 items-end">
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                                                                <input type="text" wire:model="otherLines.{{ $index }}.remark" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Rapid Test">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan Tambahan</label>
                                                                <input type="text" wire:model="otherLines.{{ $index }}.desc" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Hotel Bintang 4">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah</label>
                                                                <input type="number" wire:model="otherLines.{{ $index }}.qty" min="0" step="0.5" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Satuan</label>
                                                                <input type="number" wire:model="otherLines.{{ $index }}.unit_amount" min="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Total</label>
                                                                <div class="px-2 py-1 text-sm bg-gray-100 dark:bg-gray-600 rounded font-mono">
                                                                    Rp {{ number_format((float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0), 0, ',', '.') }}
                                                                </div>
                                                            </div>
                                                            <div class="flex items-end">
                                                                <button type="button" wire:click="removeOtherLine({{ $index }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                                                    Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                                                    Belum ada biaya lainnya yang ditambahkan
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Total Keseluruhan -->
                                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Total Keseluruhan</h4>
                                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                                Rp {{ number_format($totalAmount, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                

                            <div class="mt-6 flex items-center justify-end space-x-3">
                                <a href="{{ $this->getBackUrl() }}" 
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Batal
                                </a>
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

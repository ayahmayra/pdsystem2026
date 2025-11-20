<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Edit Kwitansi
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

                <form wire:submit="update">
                    <!-- Informasi Nota Dinas dan SPT sebagai Referensi -->
                    @if($receipt->sppd->spt && $receipt->sppd->spt->notaDinas)
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
                                <p class="text-gray-900 dark:text-white font-mono">{{ $receipt->sppd->spt->notaDinas->doc_no }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Tanggal Nota Dinas:</span>
                                <p class="text-gray-900 dark:text-white">{{ $receipt->sppd->spt->notaDinas->nd_date ? \Carbon\Carbon::parse($receipt->sppd->spt->notaDinas->nd_date)->locale('id')->translatedFormat('d F Y') : '-' }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Bidang Pengaju:</span>
                                <p class="text-gray-900 dark:text-white">{{ $receipt->sppd->spt->notaDinas->requestingUnit->name ?? '-' }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Dari:</span>
                                <p class="text-gray-900 dark:text-white">{{ $receipt->sppd->spt->notaDinas->fromUser->fullNameWithTitles() ?? '-' }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $receipt->sppd->spt->notaDinas->fromUser->position->name ?? '-' }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Kepada:</span>
                                <p class="text-gray-900 dark:text-white">{{ $receipt->sppd->spt->notaDinas->toUser->fullNameWithTitles() ?? '-' }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $receipt->sppd->spt->notaDinas->toUser->position->name ?? '-' }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Tujuan:</span>
                                <p class="text-gray-900 dark:text-white">{{ $receipt->sppd->spt->notaDinas->destinationCity->name ?? '-' }}, {{ $receipt->sppd->spt->notaDinas->destinationCity->province->name ?? '-' }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Periode Perjalanan:</span>
                                <p class="text-gray-900 dark:text-white">
                                    {{ $receipt->sppd->spt->notaDinas->start_date ? \Carbon\Carbon::parse($receipt->sppd->spt->notaDinas->start_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                    s.d
                                    {{ $receipt->sppd->spt->notaDinas->end_date ? \Carbon\Carbon::parse($receipt->sppd->spt->notaDinas->end_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    ({{ $receipt->sppd->spt->notaDinas->start_date && $receipt->sppd->spt->notaDinas->end_date ? \Carbon\Carbon::parse($receipt->sppd->spt->notaDinas->start_date)->diffInDays(\Carbon\Carbon::parse($receipt->sppd->spt->notaDinas->end_date)) + 1 : 0 }} hari)
                                </p>
                            </div>
                            <div class="space-y-1 md:col-span-2 lg:col-span-3">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Hal:</span>
                                <p class="text-gray-900 dark:text-white">{{ $receipt->sppd->spt->notaDinas->hal }}</p>
                            </div>
                            
                            <!-- SPT Info -->
                            <div class="space-y-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Nomor SPT:</span>
                                <p class="text-gray-900 dark:text-white font-mono">{{ $receipt->sppd->spt->doc_no }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Tanggal SPT:</span>
                                <p class="text-gray-900 dark:text-white">{{ $receipt->sppd->spt->spt_date ? \Carbon\Carbon::parse($receipt->sppd->spt->spt_date)->locale('id')->translatedFormat('d F Y') : '-' }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Penandatangan SPT:</span>
                                <p class="text-gray-900 dark:text-white">{{ $receipt->sppd->spt->signedByUser->fullNameWithTitles() ?? '-' }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $receipt->sppd->spt->signedByUser->position->name ?? '-' }}</p>
                            </div>
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
                                    {{ $receipt->sppd->doc_no }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tanggal SPPD
                                </label>
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $receipt->sppd->sppd_date ? \Carbon\Carbon::parse($receipt->sppd->sppd_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tujuan
                                </label>
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $receipt->sppd->spt?->notaDinas?->destinationCity?->name ?? 'N/A' }}, {{ $receipt->sppd->spt?->notaDinas?->destinationCity?->province?->name ?? 'N/A' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Penerima Pembayaran
                                </label>
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $receipt->payeeUser->fullNameWithTitles() ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $receipt->payeeUser->position?->name ?? 'N/A' }} - {{ $receipt->payeeUser->unit?->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <div class="space-y-6">

                            <!-- Peserta (Penerima Pembayaran) - Read Only -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Peserta (Penerima Pembayaran)
                                </label>
                                <div class="px-3 py-2 border border-gray-300 rounded-md bg-gray-100 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white">
                                    {{ $receipt->payeeUser->fullNameWithTitles() ?? 'N/A' }}
                                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $receipt->payeeUser->position?->name ?? 'N/A' }} - {{ $receipt->payeeUser->unit?->name ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Peserta tidak dapat diubah setelah kwitansi dibuat
                                </div>
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
                                    @php
                                        $selectedParticipant = $receipt->sppd->spt->notaDinas->participants->where('user_id', $receipt->payee_user_id)->first();
                                        $hasTravelGrade = $selectedParticipant?->user_travel_grade_id_snapshot || $selectedParticipant?->user?->travel_grade_id;
                                    @endphp
                                    @if($hasTravelGrade)
                                        <span class="text-green-600 dark:text-green-400">
                                            ✓ Tingkat perjalanan dinas peserta sudah ditentukan
                                        </span>
                                    @else
                                        <span class="text-yellow-600 dark:text-yellow-400">
                                            ⚠ Tingkat perjalanan dinas peserta belum ditentukan, silakan pilih tingkat perjalanan dinas
                                        </span>
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
                                        @if($receipt->sppd && !$receipt->sppd->sub_keg_id)
                                            <strong>Perhatian:</strong> SPPD ini belum memiliki sub kegiatan yang dipilih. Silakan edit SPPD terlebih dahulu untuk memilih sub kegiatan.
                                        @elseif($receipt->sppd && $receipt->sppd->subKeg && $receipt->sppd->subKeg->activeRekeningBelanja->count() == 0)
                                            <strong>Perhatian:</strong> Sub kegiatan "{{ $receipt->sppd->subKeg->nama_subkeg }}" belum memiliki rekening belanja yang aktif. Silakan tambahkan rekening belanja untuk sub kegiatan ini.
                                        @else
                                            Tidak ada rekening belanja yang tersedia untuk sub kegiatan ini.
                                        @endif
                                    </div>
                                @endif
                                
                                @error('rekening_belanja_id') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Reference Rates & Perhitungan Biaya (hanya tampil jika travel grade sudah dipilih) -->
                            @if($travel_grade_id)
                            <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                                <!-- Reference Rates Section -->
                                @if($airfareRate || $lodgingCap || $perdiemDailyRate || $representationRate)
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                                    <h4 class="text-md font-medium text-yellow-800 dark:text-yellow-200 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        Referensi Tarif Maksimal
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                        @if($airfareRate)
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Tiket Pesawat:</span>
                                            <p class="text-yellow-700 dark:text-yellow-300 font-mono">
                                                Rp {{ number_format($airfareRate, 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $airfareOrigin }} → {{ $airfareDestination }}
                                            </p>
                                        </div>
                                        @endif
                                        
                                        @if($lodgingCap)
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Maksimal Penginapan:</span>
                                            <p class="text-yellow-700 dark:text-yellow-300 font-mono">
                                                Rp {{ number_format($lodgingCap, 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                Provinsi {{ $lodgingProvince }}
                                            </p>
                                        </div>
                                        @endif
                                        
                                        @if($perdiemDailyRate)
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Uang Harian per Hari:</span>
                                            <p class="text-yellow-700 dark:text-yellow-300 font-mono">
                                                Rp {{ number_format($perdiemDailyRate, 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ ucfirst(str_replace('_', ' ', $perdiemTripType)) }} - {{ $perdiemProvince }}
                                            </p>
                                        </div>
                                        @endif
                                        
                                        @if($perdiemTotalAmount)
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Total Uang Harian:</span>
                                            <p class="text-yellow-700 dark:text-yellow-300 font-mono">
                                                Rp {{ number_format($perdiemTotalAmount, 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $this->calculateTripDays($receipt->sppd->spt->notaDinas) }} hari
                                            </p>
                                        </div>
                                        @endif
                                        
                                        @if($representationRate)
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Tarif Representasi:</span>
                                            <p class="text-yellow-700 dark:text-yellow-300 font-mono">
                                                Rp {{ number_format($representationRate, 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ ucfirst(str_replace('_', ' ', $representationTripType)) }}
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif
                                
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Perhitungan Biaya
                                </h3>
                                
                                <!-- Komponen Biaya -->
                                <div class="space-y-4 ">
                                    <!-- 1. Biaya Transportasi -->
                                    <div class="bg-red-100 dark:bg-gray-700 rounded-lg p-4 border border-gray-1000 dark:border-gray-600">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-medium text-gray-900 dark:text-white">1. Biaya Transportasi</h4>
                                            <button type="button" wire:click="addTransportLine" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                                + Tambah
                                            </button>
                                        </div>
                                        
                                        <!-- Reference rate warning for transport -->
                                        @if($transportIntraProvince || $transportIntraDistrict || $airfareRate)
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-3">
                                            <div class="text-xs text-blue-700 dark:text-blue-300">
                                                <strong>Referensi Tarif:</strong>
                                                @if($transportIntraProvince)
                                                    <span class="block">Dalam Provinsi: Rp {{ number_format($transportIntraProvince, 0, ',', '.') }}</span>
                                                @endif
                                                @if($transportIntraDistrict)
                                                    <span class="block">Dalam Kabupaten: Rp {{ number_format($transportIntraDistrict, 0, ',', '.') }}</span>
                                                @endif
                                                @if($airfareRate)
                                                    <span class="block">Tiket Pesawat: Rp {{ number_format($airfareRate, 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Notification for transport without reference rates -->
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3 mb-3">
                                            <div class="text-xs text-yellow-700 dark:text-yellow-300">
                                                <strong>ℹ️ Informasi:</strong>
                                                <span class="block">• Tiket Pesawat, Transport Dalam Provinsi, dan Transport Dalam Kabupaten akan otomatis terisi dengan tarif standar</span>
                                                <span class="block">• Kendaraan Dinas, Taxi, RORO, Tol, dan Parkir & Penginapan perlu diisi manual sesuai ketentuan</span>
                                                <span class="block">• ⚠️ Nilai manual yang melebihi tarif referensi akan ditampilkan peringatan</span>
                                            </div>
                                        </div>

                                        <!-- Warning Banner for Excessive Values -->
                                        @if($hasExcessiveValues)
                                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-3">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                                        ⚠️ Nilai Melebihi Standar Referensi
                                                    </h3>
                                                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                                        <p class="mb-2">Terdapat nilai yang melebihi standar referensi. Silakan sesuaikan terlebih dahulu sebelum menyimpan kwitansi:</p>
                                                        <ul class="list-disc list-inside space-y-1">
                                                            @foreach($excessiveValueDetails as $detail)
                                                            <li>
                                                                <strong>{{ $detail['type'] }}:</strong> 
                                                                Rp {{ number_format($detail['manual_value'], 0, ',', '.') }} 
                                                                (melebihi Rp {{ number_format($detail['reference_value'], 0, ',', '.') }} 
                                                                sebesar Rp {{ number_format($detail['excess_amount'], 0, ',', '.') }} 
                                                                atau {{ $detail['excess_percentage'] }}%)
                                                            </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if(count($transportLines) > 0)
                                            <div class="space-y-3">
                                                @foreach($transportLines as $index => $line)
                                                <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 p-3">
                                                    <div class="grid grid-cols-12 gap-3 items-end">
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
                                                                @if($line['has_reference'])
                                                                    <span class="text-green-600 dark:text-green-400">✓ Auto-filled</span>
                                                                @endif
                                                                @if($line['is_overridden'])
                                                                    <span class="text-blue-600 dark:text-blue-400">✏️ Manual</span>
                                                                @endif
                                                            </label>
                                                            <input type="number" 
                                                                wire:model.live="transportLines.{{ $index }}.unit_amount" 
                                                                min="0" 
                                                                class="w-full h-10 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $line['has_reference'] ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-600' : ($line['is_overridden'] ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-600' : '') }}"
                                                                {{ $line['has_reference'] ? 'readonly' : '' }}
                                                                placeholder="{{ $line['has_reference'] ? 'Otomatis terisi' : 'Masukkan harga satuan' }}">
                                                            
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
                                                        <div class="col-span-2 flex items-center space-x-2 h-10">
                                                            @if($line['has_reference'])
                                                                <button type="button" 
                                                                    wire:click="overrideTransportRate({{ $index }})" 
                                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs">
                                                                    Edit Manual
                                                                </button>
                                                            @endif
                                                            <button type="button" wire:click="removeTransportLine({{ $index }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
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
                                    <div class="bg-yellow-100 dark:bg-gray-700 rounded-lg p-4 border border-gray-1000 dark:border-gray-600">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-medium text-gray-900 dark:text-white">2. Biaya Penginapan</h4>
                                            <button type="button" wire:click="addLodgingLine" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                                + Tambah
                                            </button>
                                        </div>
                                        
                                        <!-- Reference rate warning for lodging -->
                                        @if($lodgingCap)
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-3">
                                            <div class="text-xs text-blue-700 dark:text-blue-300">
                                                <strong>Referensi Tarif:</strong>
                                                <span class="block">Maksimal per Malam: Rp {{ number_format($lodgingCap, 0, ',', '.') }}</span>
                                                <span class="block">Provinsi: {{ $lodgingProvince }}</span>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if(count($lodgingLines) > 0)
                                            <div class="space-y-3">
                                                @foreach($lodgingLines as $index => $line)
                                                <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 p-3">
                                                    <div class="grid grid-cols-12 gap-3 items-end">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Kota Tujuan</label>
                                                            <select wire:model.live="lodgingLines.{{ $index }}.destination_city_id" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                                <option value="">Tujuan Utama</option>
                                                                @foreach($availableCities as $city)
                                                                    <option value="{{ $city->id }}">{{ $city->name }}, {{ $city->province->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Malam</label>
                                                            <input type="number" wire:model="lodgingLines.{{ $index }}.qty" min="0" step="0.5" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan Tambahan</label>
                                                            <input type="text" wire:model="lodgingLines.{{ $index }}.desc" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Hotel Bintang 4">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                <input type="checkbox" wire:model.live="lodgingLines.{{ $index }}.no_lodging" class="mr-2">
                                                                Tidak Menginap
                                                            </label>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                (30% dari tarif penginapan)
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Tarif per Malam
                                                                @if($line['has_reference'])
                                                                    <span class="text-green-600 dark:text-green-400">✓ Referensi</span>
                                                                @endif
                                                                @if($line['is_overridden'])
                                                                    <span class="text-blue-600 dark:text-blue-400">✏️ Manual</span>
                                                                @endif
                                                            </label>
                                                            <input type="number" 
                                                                wire:model.live="lodgingLines.{{ $index }}.unit_amount" 
                                                                min="0" 
                                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $line['has_reference'] ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-600' : ($line['is_overridden'] ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-600' : '') }}"
                                                                {{ $line['has_reference'] ? 'readonly' : '' }}
                                                                placeholder="{{ $line['has_reference'] ? 'Otomatis terisi' : 'Masukkan tarif per malam' }}">
                                                            
                                                            <!-- Rate Info Display -->
                                                            @if($line['rate_info'])
                                                            <div class="mt-1 text-xs {{ $line['has_reference'] ? 'text-green-600 dark:text-green-400' : ($line['is_overridden'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400') }}">
                                                                @if($line['has_reference'])
                                                                    ✓ {{ $line['rate_info'] }}
                                                                @elseif($line['is_overridden'])
                                                                    ✏️ {{ $line['rate_info'] }}
                                                                @else
                                                                    {{ $line['rate_info'] }}
                                                                @endif
                                                            </div>
                                                            @endif
                                                            
                                                            <!-- Warning for manual values exceeding reference -->
                                                            @if($line['exceeds_reference'])
                                                            <div class="mt-1 p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded text-xs">
                                                                <div class="text-red-700 dark:text-red-300 font-medium">
                                                                    ⚠️ Nilai melebihi tarif referensi!
                                                                </div>
                                                                <div class="text-red-600 dark:text-red-400 mt-1">
                                                                    <span class="block">• Tarif referensi: Rp {{ number_format($line['original_reference_rate'], 0, ',', '.') }}</span>
                                                                    <span class="block">• Nilai manual: Rp {{ number_format($line['unit_amount'], 0, ',', '.') }}</span>
                                                                    <span class="block">• Kelebihan: Rp {{ number_format($line['excess_amount'], 0, ',', '.') }} ({{ $line['excess_percentage'] }}%)</span>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Total</label>
                                                            <div class="px-2 py-1 text-sm bg-gray-100 dark:bg-gray-600 rounded font-mono">
                                                                Rp {{ number_format((float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0), 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                        <div class="flex items-end space-x-2">
                                                            @if($line['has_reference'])
                                                                <button type="button" 
                                                                    wire:click="overrideLodgingRate({{ $index }})" 
                                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs">
                                                                    Edit Manual
                                                                </button>
                                                            @endif
                                                            <button type="button" wire:click="removeLodgingLine({{ $index }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
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
                                    <div class="bg-green-200 dark:bg-gray-700 rounded-lg p-4 border border-gray-1000 dark:border-gray-600">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-medium text-gray-900 dark:text-white">3. Uang Harian (Perdiem)</h4>
                                            <button type="button" wire:click="addPerdiemLine" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                                + Tambah
                                            </button>
                                        </div>
                                        
                                        <!-- Reference rate warning for perdiem -->
                                        @if($perdiemDailyRate)
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-3">
                                            <div class="text-xs text-blue-700 dark:text-blue-300">
                                                <strong>Referensi Tarif:</strong>
                                                <span class="block">Tarif per Hari: Rp {{ number_format($perdiemDailyRate, 0, ',', '.') }}</span>
                                                <span class="block">Provinsi: {{ $perdiemProvince }}</span>
                                                <span class="block">Jenis Perjalanan: {{ ucfirst(str_replace('_', ' ', $perdiemTripType)) }}</span>
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
                                    <div class="bg-teal-200 dark:bg-gray-700 rounded-lg p-4 border border-gray-1000 dark:border-gray-600">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-medium text-gray-900 dark:text-white">4. Biaya Representatif</h4>
                                            <button type="button" wire:click="addRepresentationLine" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                                + Tambah
                                            </button>
                                        </div>
                                        
                                        <!-- Reference rate warning for representation -->
                                        @if($representationRate)
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-3">
                                            <div class="text-xs text-blue-700 dark:text-blue-300">
                                                <strong>Referensi Tarif:</strong>
                                                <span class="block">Tarif Representasi: Rp {{ number_format($representationRate, 0, ',', '.') }}</span>
                                                <span class="block">Jenis Perjalanan: {{ ucfirst(str_replace('_', ' ', $representationTripType)) }}</span>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if(count($representationLines) > 0)
                                            <div class="space-y-3">
                                                @foreach($representationLines as $index => $line)
                                                <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 p-3">
                                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah</label>
                                                            <input type="number" wire:model="representationLines.{{ $index }}.qty" min="0" step="0.5" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Satuan</label>
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
                                    <div class="bg-indigo-200 dark:bg-gray-700 rounded-lg p-4 border border-gray-1000 dark:border-gray-600">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-medium text-gray-900 dark:text-white">5. Biaya Lainnya</h4>
                                            <button type="button" wire:click="addOtherLine" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                                + Tambah
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
                                    placeholder="Nomor dari aplikasi SIPD (opsional)"
                                />
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Nomor kwitansi akan diisi dari aplikasi SIPD. Bisa dikosongkan untuk sementara.
                                </div>
                                @error('receipt_no') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-3">
                            <a href="{{ $this->getBackUrl() }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

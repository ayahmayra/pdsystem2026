<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Generate SPPD</h1>
            @if($spt)
                <p class="text-sm text-gray-600 dark:text-gray-300">Berdasarkan SPT: <span class="font-mono">{{ $spt->doc_no }}</span></p>
            @endif
        </div>
        <a href="{{ route('documents', ['nota_dinas_id' => $spt->nota_dinas_id, 'spt_id' => $spt->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Kembali</a>
    </div>

    @if (session('error'))
        <div class="p-3 rounded-md bg-red-100 text-red-800 border border-red-200">{{ session('error') }}</div>
    @endif
    @if (session('message'))
        <div class="p-3 rounded-md bg-green-100 text-green-800 border border-green-200">{{ session('message') }}</div>
    @endif

    <!-- Informasi Nota Dinas dan SPT sebagai Referensi -->
    @if($spt && $spt->notaDinas)
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
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
                <p class="text-gray-900 dark:text-white font-mono">{{ $spt->notaDinas->doc_no }}</p>
            </div>
            <div class="space-y-1">
                <span class="font-medium text-gray-700 dark:text-gray-300">Tanggal Nota Dinas:</span>
                <p class="text-gray-900 dark:text-white">{{ $spt->notaDinas->nd_date ? \Carbon\Carbon::parse($spt->notaDinas->nd_date)->locale('id')->translatedFormat('d F Y') : '-' }}</p>
            </div>
            <div class="space-y-1">
                <span class="font-medium text-gray-700 dark:text-gray-300">Bidang Pengaju:</span>
                <p class="text-gray-900 dark:text-white">{{ $spt->notaDinas->requestingUnit->name ?? '-' }}</p>
            </div>
            <div class="space-y-1">
                <span class="font-medium text-gray-700 dark:text-gray-300">Dari:</span>
                <p class="text-gray-900 dark:text-white">{{ $spt->notaDinas->fromUser->fullNameWithTitles() ?? '-' }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $spt->notaDinas->fromUser->position->name ?? '-' }}</p>
            </div>
            <div class="space-y-1">
                <span class="font-medium text-gray-700 dark:text-gray-300">Kepada:</span>
                <p class="text-gray-900 dark:text-white">{{ $spt->notaDinas->toUser->fullNameWithTitles() ?? '-' }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $spt->notaDinas->toUser->position->name ?? '-' }}</p>
            </div>
            <div class="space-y-1">
                <span class="font-medium text-gray-700 dark:text-gray-300">Tujuan:</span>
                <p class="text-gray-900 dark:text-white">{{ $spt->notaDinas->destinationCity->name ?? '-' }}, {{ $spt->notaDinas->destinationCity->province->name ?? '-' }}</p>
            </div>
            <div class="space-y-1">
                <span class="font-medium text-gray-700 dark:text-gray-300">Periode Perjalanan:</span>
                <p class="text-gray-900 dark:text-white">
                    {{ $spt->notaDinas->start_date ? \Carbon\Carbon::parse($spt->notaDinas->start_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                    s.d
                    {{ $spt->notaDinas->end_date ? \Carbon\Carbon::parse($spt->notaDinas->end_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    ({{ $spt->notaDinas->start_date && $spt->notaDinas->end_date ? \Carbon\Carbon::parse($spt->notaDinas->start_date)->diffInDays(\Carbon\Carbon::parse($spt->notaDinas->end_date)) + 1 : 0 }} hari)
                </p>
            </div>
            <div class="space-y-1 md:col-span-2 lg:col-span-3">
                <span class="font-medium text-gray-700 dark:text-gray-300">Hal:</span>
                <p class="text-gray-900 dark:text-white">{{ $spt->notaDinas->hal }}</p>
            </div>
            
            <!-- SPT Info -->
            <div class="space-y-1">
                <span class="font-medium text-gray-700 dark:text-gray-300">Nomor SPT:</span>
                <p class="text-gray-900 dark:text-white font-mono">{{ $spt->doc_no }}</p>
            </div>
            <div class="space-y-1">
                <span class="font-medium text-gray-700 dark:text-gray-300">Tanggal SPT:</span>
                <p class="text-gray-900 dark:text-white">{{ $spt->spt_date ? \Carbon\Carbon::parse($spt->spt_date)->locale('id')->translatedFormat('d F Y') : '-' }}</p>
            </div>
            <div class="space-y-1">
                <span class="font-medium text-gray-700 dark:text-gray-300">Penandatangan SPT:</span>
                <p class="text-gray-900 dark:text-white">{{ $spt->signedByUser->fullNameWithTitles() ?? '-' }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $spt->signedByUser->position->name ?? '-' }}</p>
            </div>
            
            @if($spt->notaDinas->participants && $spt->notaDinas->participants->count() > 0)
            <div class="space-y-1 md:col-span-2 lg:col-span-3">
                <span class="font-medium text-gray-700 dark:text-gray-300">Peserta Perjalanan:</span>
                <div class="flex flex-wrap gap-2 mt-1">
                    @foreach($spt->notaDinas->getSortedParticipants() as $participant)
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

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <form wire:submit="save" class="space-y-6 p-6">
            @if($format_string)
                <div class="rounded-md border border-gray-200 dark:border-gray-700 p-3 bg-gray-50 dark:bg-gray-700/30 text-xs text-gray-700 dark:text-gray-300">
                    <div>Format aktif: <code class="px-1 py-0.5 bg-white/70 dark:bg-black/30 rounded">{{ $format_string }}</code></div>
                    <div>Contoh nomor berikutnya: <span class="font-mono">{{ $format_example }}</span></div>
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal SPPD <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="sppd_date" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    @error('sppd_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Moda Transportasi <span class="text-red-500">*</span></label>
                    <div class="border rounded-md p-2 max-h-44 overflow-y-auto">
                        @foreach($transportModes as $tm)
                            <label class="flex items-center gap-2 py-1">
                                <input type="checkbox" value="{{ $tm->id }}" wire:model="transport_mode_ids" />
                                <span>{{ $tm->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('transport_mode_ids')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tempat Berangkat (Origin)</label>
                    <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-600 dark:text-gray-400">
                        <strong>Otomatis dari Nota Dinas:</strong> {{ $spt->notaDinas?->originPlace?->name ?? 'Belum diisi di Nota Dinas' }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Perjalanan</label>
                    <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-600 dark:text-gray-400">
                        <strong>Otomatis dari Nota Dinas:</strong> 
                        @switch($spt->notaDinas?->trip_type)
                            @case('LUAR_DAERAH')
                                Luar Daerah
                                @break
                            @case('DALAM_DAERAH_GT8H')
                                Dalam Daerah > 8 Jam
                                @break
                            @case('DALAM_DAERAH_LE8H')
                                Dalam Daerah ≤ 8 Jam
                                @break
                            @case('DIKLAT')
                                Diklat
                                @break
                            @default
                                Belum diisi di Nota Dinas
                        @endswitch
                    </div>
                </div>
                
                {{-- <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lama Perjalanan (hari) <span class="text-red-500">*</span></label>
                    <input type="number" min="1" wire:model="days_count" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    @error('days_count')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div> --}}
                
            </div>

            <!-- Penandatangan dan Assignment Title -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Penandatangan <span class="text-red-500">*</span></label>
                    <select wire:model="signed_by_user_id" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Pilih penandatangan</option>
                        @foreach(\App\Models\User::where('is_signer', true)->orderBy('name')->get() as $signer)
                            <option value="{{ $signer->id }}">{{ $signer->fullNameWithTitles() }} ({{ trim(($signer->position?->name ?? '') . ' ' . ($signer->unit?->name ?? '')) ?: '-' }})</option>
                        @endforeach
                    </select>
                    @error('signed_by_user_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan pada SPPD (Assignment Title)</label>
                    
                    <!-- Toggle untuk custom assignment title -->
                    <div class="flex items-center gap-2 mb-2">
                        <input type="checkbox" id="use_custom_assignment_title" wire:model.live="use_custom_assignment_title" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        <label for="use_custom_assignment_title" class="text-sm text-gray-700 dark:text-gray-300">Gunakan custom assignment title</label>
                    </div>
                    
                    @if($use_custom_assignment_title)
                        <!-- Custom assignment title dengan textarea -->
                        <textarea 
                            wire:model="assignment_title" 
                            rows="3"
                            placeholder="Masukkan custom assignment title (bisa beberapa baris)..." 
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-y"
                        ></textarea>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Assignment title custom akan digunakan sebagai jabatan pada SPPD.</p>
                    @else
                        <!-- Auto assignment title (read-only) -->
                        <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md">
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $assignment_title ?: 'Tidak ada assignment title yang tersedia' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Assignment title otomatis dari jabatan penandatangan.</p>
                        </div>
                    @endif
                    
                    @error('assignment_title')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>


            <!-- Sub Kegiatan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Sub Kegiatan <span class="text-red-500">*</span>
                </label>
                <flux:select wire:model="sub_keg_id" variant="listbox" searchable placeholder="Pilih Sub Kegiatan...">
                    <flux:select.option value="">Pilih Sub Kegiatan</flux:select.option>
                    @foreach($subKegiatan as $subKeg)
                        <flux:select.option value="{{ $subKeg->id }}">
                            {{ $subKeg->kode_subkeg }} - {{ $subKeg->nama_subkeg }}
                            @if($subKeg->unit)
                                ({{ $subKeg->unit->name }})
                            @endif
                            - Pagu: Rp {{ number_format($subKeg->total_pagu, 0, ',', '.') }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                @error('sub_keg_id') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
                @if(!$sub_keg_id)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sub Kegiatan wajib dipilih untuk menentukan alokasi anggaran.</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Peserta yang Akan Dibuatkan SPPD
                </label>
                <div class="mb-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                    <p class="text-sm text-green-800 dark:text-green-200">
                        <strong>Info:</strong> 1 SPPD akan dibuat untuk mewakili semua peserta perjalanan dari Nota Dinas ini.
                    </p>
                </div>
                <div class="border rounded-md max-h-56 overflow-y-auto p-2">
                    @foreach($participants as $p)
                        <div class="flex items-center gap-3 py-1">
                            <span class="text-green-600">✓</span>
                            <span>{{ $p['name'] }} <span class="text-xs text-gray-500">NIP {{ $p['nip'] }}</span></span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('documents', ['nota_dinas_id' => $spt->nota_dinas_id, 'spt_id' => $spt->id]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">Batal</a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Generate SPPD</button>
            </div>
        </form>
    </div>
</div>

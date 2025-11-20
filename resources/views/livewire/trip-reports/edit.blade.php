<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex items-center mb-6">
                <a href="{{ $this->getBackUrl() }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    ← Kembali ke Dokumen
                </a>
                <h2 class="text-2xl font-bold text-gray-900">Edit Laporan Perjalanan Dinas</h2>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form wire:submit="update">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Informasi Dokumen Lengkap -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Informasi Dokumen (Referensi)
                                </h3>
                                
                                <!-- Nomor Dokumen -->
                                <div class="mb-4">
                                    <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-2">Nomor Dokumen:</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">1. Nota Dinas:</span>
                                            <p class="text-gray-900 dark:text-white font-mono">{{ $tripReport->spt->notaDinas->doc_no ?? '-' }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $tripReport->spt->notaDinas->doc_date ? \Carbon\Carbon::parse($tripReport->spt->notaDinas->doc_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                            </p>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">2. SPT:</span>
                                            <p class="text-gray-900 dark:text-white font-mono">{{ $tripReport->spt->doc_no }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $tripReport->spt->doc_date ? \Carbon\Carbon::parse($tripReport->spt->doc_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                            </p>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">3. SPPD:</span>
                                            <p class="text-gray-900 dark:text-white font-mono">{{ $tripReport->spt->sppds->first()?->doc_no ?? '-' }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $tripReport->spt->sppds->first()?->doc_date ? \Carbon\Carbon::parse($tripReport->spt->sppds->first()->doc_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Perjalanan -->
                                <div class="mb-4">
                                    <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-2">Informasi Perjalanan:</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Bidang Pengaju:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $tripReport->spt->notaDinas->requestingUnit->name ?? '-' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Sifat:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $tripReport->spt->notaDinas->sifat ?? '-' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Lampiran:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $tripReport->spt->notaDinas->lampiran_count ?? 0 }} lembar</p>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Dari:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $tripReport->spt->notaDinas->fromUser->fullNameWithTitles() ?? '-' }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $tripReport->spt->notaDinas->fromUser->position->name ?? '-' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Kepada:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $tripReport->spt->notaDinas->toUser->fullNameWithTitles() ?? '-' }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $tripReport->spt->notaDinas->toUser->position->name ?? '-' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Tembusan:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $tripReport->spt->notaDinas->tembusan ?? '-' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Tempat Asal:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $tripReport->spt->notaDinas->originPlace->name ?? '-' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Tujuan:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $tripReport->spt->notaDinas->destinationCity->name ?? '-' }}, {{ $tripReport->spt->notaDinas->destinationCity->province->name ?? '-' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Periode Perjalanan:</span>
                                            <p class="text-gray-900 dark:text-white">
                                                {{ $tripReport->spt->notaDinas->start_date ? \Carbon\Carbon::parse($tripReport->spt->notaDinas->start_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                                s.d
                                                {{ $tripReport->spt->notaDinas->end_date ? \Carbon\Carbon::parse($tripReport->spt->notaDinas->end_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                ({{ $tripReport->spt->notaDinas->days_count ?? ($tripReport->spt->notaDinas->start_date && $tripReport->spt->notaDinas->end_date ? \Carbon\Carbon::parse($tripReport->spt->notaDinas->start_date)->diffInDays(\Carbon\Carbon::parse($tripReport->spt->notaDinas->end_date)) + 1 : 0) }} hari)
                                            </p>
                                        </div>
                                        <div class="space-y-1 md:col-span-2 lg:col-span-3">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Hal:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $tripReport->spt->notaDinas->hal }}</p>
                                        </div>
                                    </div>

                                    <!-- Dasar dan Maksud -->
                                    <div class="mb-4">
                                        <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-2">Dasar dan Maksud:</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div class="space-y-1">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Dasar:</span>
                                                <p class="text-gray-900 dark:text-white">{{ $tripReport->spt->notaDinas->dasar ?? '-' }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Maksud:</span>
                                                <p class="text-gray-900 dark:text-white">{{ $tripReport->spt->notaDinas->maksud ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    </div>
                                </div>

                                <!-- Daftar Peserta -->
                                @if($tripReport->spt->notaDinas->participants && $tripReport->spt->notaDinas->participants->count() > 0)
                                <div>
                                    <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-2">Daftar Peserta Perjalanan:</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($tripReport->spt->notaDinas->getSortedParticipants() as $participant)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $participant->user->fullNameWithTitles() }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                                <div class="space-y-6">
                                    <!-- Tanggal Laporan -->
                                    <div>
                                        <label for="report_date" class="block text-sm font-medium text-gray-700">
                                            Tanggal Laporan <span class="text-red-500">*</span>
                                        </label>
                                        <input wire:model="report_date" type="date" id="report_date" 
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        @error('report_date') 
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Nomor Laporan -->
                                    <div>
                                        <label for="report_no" class="block text-sm font-medium text-gray-700">
                                            Nomor Laporan (opsional)
                                        </label>
                                        <input wire:model="report_no" type="text" id="report_no" 
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                               placeholder="Biarkan kosong jika belum ada">
                                    </div>

                                    <!-- Kegiatan -->
                                    <div>
                                        <x-enhanced-textarea 
                                            id="activities"
                                            model="activities"
                                            label="Kegiatan yang Dilakukan <span class='text-red-500'>*</span>"
                                            placeholder="Jelaskan kegiatan yang dilakukan selama perjalanan dinas... Gunakan bullet points atau numbering untuk struktur yang lebih baik."
                                            rows="8"
                                        />
                                        @error('activities') 
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                        <div class="mt-6 flex items-center justify-end space-x-3">
                            <a href="{{ $this->getBackUrl() }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div>
    <!-- Search and Filters -->
    <div class="mb-4" style="padding: 10px;">
        <div class="flex flex-wrap items-center gap-3">
            <!-- Search -->
            <div class="flex-1 min-w-0">
                <input 
                    wire:model.live.debounce.400ms="search" 
                    type="text" 
                    placeholder="Cari berdasarkan nomor, unit, tujuan, atau nama peserta..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Date From -->
            <div class="w-40">
                <input 
                    wire:model.live="dateFrom" 
                    type="date" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Date To -->
            <div class="w-40">
                <input 
                    wire:model.live="dateTo" 
                    type="date" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Unit Filter -->
            <div class="w-48">
                <select 
                    wire:model.live="unitFilter" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Semua Unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-40">
                <select 
                    wire:model.live="statusFilter" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Semua Status</option>
                    <option value="DRAFT">Draft</option>
                    <option value="APPROVED">Approved</option>
                </select>
            </div>

            <!-- Reset Button -->
            <div>
                <button 
                    wire:click="resetFilters"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Active Filters Indicator -->
    @if($search || $dateFrom || $dateTo || $unitFilter || $statusFilter)
        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Filter Aktif:</span>
                </div>
                <button wire:click="resetFilters" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">
                    Reset Semua
                </button>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
                @if($search)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                        Pencarian: "{{ $search }}"
                    </span>
                @endif
                @if($dateFrom)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                        Dari: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
                    </span>
                @endif
                @if($dateTo)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                        Sampai: {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
                    </span>
                @endif
                @if($unitFilter)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                        Unit: {{ $units->firstWhere('id', $unitFilter)?->name ?? 'Unknown' }}
                    </span>
                @endif
                @if($statusFilter)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                        Status: {{ $statusFilter }}
                    </span>
                @endif
            </div>
        </div>
    @endif

    <!-- Nota Dinas Table -->
    <div class="overflow-x-auto w-full">
        <table class="w-full min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Nomor & Tanggal
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Unit
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Tujuan
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Peserta
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Lama
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($notaDinasList as $notaDinas)
                    <tr 
                        wire:key="nd-{{ $notaDinas->id }}"
                        class="cursor-pointer transition-colors {{ $selectedNotaDinasId && $selectedNotaDinasId == $notaDinas->id ? 'bg-blue-200 dark:bg-blue-800' : 'bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800' }}"
                        @if($selectedNotaDinasId && $selectedNotaDinasId == $notaDinas->id)
                            style="background-color: #dbeafe !important;"
                        @endif
                        wire:click="selectNotaDinas({{ $notaDinas->id }})"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        wire:loading.attr="disabled"
                    >
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $notaDinas->doc_no }}
                                    </div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ \Carbon\Carbon::parse($notaDinas->nd_date)->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $notaDinas->requestingUnit->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $notaDinas->destinationCity->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $notaDinas->status === 'APPROVED' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                {{ $notaDinas->status === 'APPROVED' ? 'Disetujui' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-900 dark:text-white">
                            @if($notaDinas->participants->count() > 0)
                                @php
                                    $sortedParticipants = $notaDinas->getSortedParticipants();
                                @endphp
                                <div class="space-y-1">
                                    @foreach($sortedParticipants as $participant)
                                        <div class="truncate">
                                            {{ $participant->user_name_snapshot ?: $participant->user->name }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400 dark:text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-900 dark:text-white">
                            @if($notaDinas->start_date && $notaDinas->end_date)
                                <div class="space-y-1">
                                    <div class="truncate">
                                        <span class="font-medium">Mulai:</span> {{ \Carbon\Carbon::parse($notaDinas->start_date)->format('d/m/Y') }}
                                    </div>
                                    <div class="truncate">
                                        <span class="font-medium">Kembali:</span> {{ \Carbon\Carbon::parse($notaDinas->end_date)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-gray-500 dark:text-gray-400">
                                        ({{ \Carbon\Carbon::parse($notaDinas->start_date)->diffInDays(\Carbon\Carbon::parse($notaDinas->end_date)) + 1 }} hari)
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 dark:text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                <flux:menu>
                                    <!-- Cetak -->
                                    <flux:menu.item 
                                        href="{{ route('nota-dinas.pdf', $notaDinas) }}" 
                                        target="_blank"
                                        icon="printer"
                                    >
                                        Cetak
                                    </flux:menu.item>

                                    <!-- Edit -->
                                    <flux:menu.item 
                                        href="{{ route('nota-dinas.edit', $notaDinas) }}"
                                        icon="pencil-square"
                                    >
                                        Edit
                                    </flux:menu.item>

                                    <!-- Separator -->
                                    <flux:menu.separator />

                                    <!-- Delete -->
                                    @php
                                        $hasActiveSpt = $notaDinas->spt && $notaDinas->spt->exists;
                                        $hasActiveSupportingDocs = $notaDinas->supportingDocuments && $notaDinas->supportingDocuments->where('is_active', true)->count() > 0;
                                    @endphp
                                    @if($hasActiveSpt || $hasActiveSupportingDocs)
                                        <flux:menu.item 
                                            disabled
                                            icon="trash"
                                            title="Tidak dapat dihapus karena memiliki dokumen turunan (SPT atau dokumen pendukung)"
                                        >
                                            Hapus
                                        </flux:menu.item>
                                    @else
                                        <flux:menu.item 
                                            wire:click="confirmDelete({{ $notaDinas->id }})"
                                            wire:confirm="Apakah Anda yakin ingin menghapus Nota Dinas ini?"
                                            variant="danger"
                                            icon="trash"
                                        >
                                            Hapus
                                        </flux:menu.item>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>Tidak ada Nota Dinas ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modern Pagination -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <!-- Pagination Info -->
        <div class="text-sm text-gray-600 dark:text-gray-400">
            <span class="font-medium text-gray-900 dark:text-white">{{ $notaDinasList->firstItem() ?? 0 }}-{{ $notaDinasList->lastItem() ?? 0 }}</span>
            dari 
            <span class="font-medium text-gray-900 dark:text-white">{{ $notaDinasList->total() }}</span>
            Nota Dinas
        </div>
        
        <!-- Modern Pagination Navigation -->
        @if($notaDinasList->hasPages())
            <div class="flex items-center space-x-1 p-2">
                <!-- Previous Button -->
                @if($notaDinasList->onFirstPage())
                    <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-l-md cursor-not-allowed">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Sebelumnya
                    </span>
                @else
                    <button wire:click="previousPage" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-l-md hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Sebelumnya
                    </button>
                @endif

                <!-- Page Numbers -->
                <div class="flex items-center space-x-1">
                    @foreach($notaDinasList->getUrlRange(1, $notaDinasList->lastPage()) as $page => $url)
                        @if($page == $notaDinasList->currentPage())
                            <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-md">
                                {{ $page }}
                            </span>
                        @else
                            <button wire:click="gotoPage({{ $page }})" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                </div>

                <!-- Next Button -->
                @if($notaDinasList->hasMorePages())
                    <button wire:click="nextPage" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-r-md hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Selanjutnya
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                @else
                    <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-r-md cursor-not-allowed">
                        Selanjutnya
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </span>
                @endif
            </div>
        @endif
    </div>
</div>

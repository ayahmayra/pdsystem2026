<div>
    @if($sptId)
        @if(count($sppds) > 0)
            <div class="overflow-x-auto w-full">
                <table class="w-full min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Nomor & Tanggal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Pegawai
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Penandatangan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                PPTK
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Perjalanan
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($sppds as $sppd)
                            <tr 
                                wire:key="sppd-{{ $sppd->id }}"
                                class="cursor-pointer transition-colors {{ $selectedSppdId && $selectedSppdId == $sppd->id ? 'bg-green-200 dark:bg-green-800' : 'bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800' }}"
                                @if($selectedSppdId && $selectedSppdId == $sppd->id)
                                    style="background-color: #dcfce7 !important;"
                                @endif
                                wire:click="selectSppd({{ $sppd->id }})"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                wire:loading.attr="disabled"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $sppd->doc_no }}
                                            </div>
                                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ \Carbon\Carbon::parse($sppd->sppd_date)->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @php
                                        $participants = $sppd->getSortedParticipantsSnapshot();
                                    @endphp
                                    @if($participants->count() > 0)
                                        @foreach($participants as $index => $participant)
                                            <div class="{{ $index > 0 ? 'mt-1' : '' }}">
                                                {{ $index + 1 }}. {{ $participant['name'] ?? 'N/A' }}
                                                @if($participant['nip'])
                                                    <span class="text-xs text-gray-500">({{ $participant['nip'] }})</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($sppd->signedByUser)
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $sppd->signedByUser->fullNameWithTitles() ?? 'N/A' }}
                                        </div>
                                        @if($sppd->assignment_title)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ Str::limit($sppd->assignment_title, 50) }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($sppd->subKeg && $sppd->subKeg->pptkUser)
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $sppd->subKeg->pptkUser->fullNameWithTitles() }}
                                        </div>
                                        @if($sppd->subKeg->pptkUser->position)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $sppd->subKeg->pptkUser->position->name }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <div class="font-medium">{{ $sppd->spt?->notaDinas?->originPlace?->name ?? 'N/A' }} → {{ $sppd->spt?->notaDinas?->destinationCity?->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            @if($sppd->transportModes && $sppd->transportModes->count() > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-1">
                                                    {{ $sppd->transportModes->pluck('name')->implode(', ') }}
                                                </span>
                                            @endif
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                                @switch($sppd->trip_type)
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
                                                        {{ $sppd->trip_type }}
                                                @endswitch
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">


                                        <!-- Dropdown menu -->
                                        <flux:dropdown position="bottom" align="end">
                                            <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                            <flux:menu>
                                                <!-- Cetak -->
                                                <flux:menu.item 
                                                    href="{{ route('sppd.pdf', $sppd) }}" 
                                                    target="_blank"
                                                    icon="printer"
                                                >
                                                    Cetak
                                                </flux:menu.item>

                                                <!-- Edit -->
                                                <flux:menu.item 
                                                    href="{{ route('sppd.edit', $sppd) }}"
                                                    icon="pencil-square"
                                                >
                                                    Edit
                                                </flux:menu.item>

                                                <!-- Separator -->
                                                <flux:menu.separator />

                                                <!-- Delete -->
                                                @if(($sppd->itineraries && $sppd->itineraries->count() > 0) || ($sppd->receipts && $sppd->receipts->count() > 0))
                                                    <flux:menu.item 
                                                        disabled
                                                        icon="trash"
                                                        title="Tidak dapat dihapus karena memiliki data terkait"
                                                    >
                                                        Hapus
                                                    </flux:menu.item>
                                                @else
                                                    <flux:menu.item 
                                                        wire:click="confirmDelete({{ $sppd->id }})"
                                                        wire:confirm="Apakah Anda yakin ingin menghapus SPPD ini?"
                                                        variant="danger"
                                                        icon="trash"
                                                    >
                                                        Hapus
                                                    </flux:menu.item>
                                                @endif
                                            </flux:menu>
                                        </flux:dropdown>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <!-- Debug info -->
                
                
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mb-4">Belum ada SPPD untuk SPT ini</p>
                
                <button 
                    wire:click="createSppd({{ $sptId }})"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                    style="display: inline-flex !important; visibility: visible !important; opacity: 1 !important;"
                >
                    Buat SPPD
                </button>
            </div>
        @endif
    @else

        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p>Pilih SPT untuk melihat SPPD</p>

        </div>
    @endif
</div>

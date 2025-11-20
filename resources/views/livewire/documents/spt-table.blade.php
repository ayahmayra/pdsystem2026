<div>
    @if($notaDinasId)
        @if(count($spts) > 0)
            <div class="overflow-x-auto w-full">
                <table class="w-full min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Nomor & Tanggal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Penugasan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Penandatangan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                SPPD
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($spts as $spt)
                            <tr 
                                wire:key="spt-{{ $spt->id }}"
                                class="cursor-pointer transition-colors {{ $selectedSptId && $selectedSptId == $spt->id ? 'bg-purple-200 dark:bg-purple-800' : 'bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800' }}"
                                @if($selectedSptId && $selectedSptId == $spt->id)
                                    style="background-color: #e9d5ff !important;"
                                @endif
                                wire:click="selectSpt({{ $spt->id }})"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                wire:loading.attr="disabled"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $spt->doc_no }}
                                            </div>
                                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ \Carbon\Carbon::parse($spt->spt_date)->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    @if($spt->assignment_title)
                                        <div class="whitespace-pre-line max-w-xs">
                                            {{ $spt->assignment_title }}
                                        </div>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">Tidak ada judul tugas</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $spt->signedByUser->fullNameWithTitles() ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                        {{ $spt->sppds->count() }} SPPD
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <flux:dropdown position="bottom" align="end">
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                        <flux:menu>
                                            <!-- Cetak -->
                                            <flux:menu.item 
                                                href="{{ route('spt.pdf', $spt) }}" 
                                                target="_blank"
                                                icon="printer"
                                            >
                                                Cetak
                                            </flux:menu.item>

                                            <!-- Edit -->
                                            <flux:menu.item 
                                                href="{{ route('spt.edit', $spt) }}"
                                                icon="pencil-square"
                                            >
                                                Edit
                                            </flux:menu.item>

                                            <!-- Separator -->
                                            <flux:menu.separator />

                                            <!-- Delete -->
                                            @if($spt->sppds && $spt->sppds->count() > 0)
                                                <flux:menu.item 
                                                    disabled
                                                    icon="trash"
                                                    title="Tidak dapat dihapus karena memiliki SPPD"
                                                >
                                                    Hapus
                                                </flux:menu.item>
                                            @else
                                                <flux:menu.item 
                                                    wire:click="confirmDelete({{ $spt->id }})"
                                                    wire:confirm="Apakah Anda yakin ingin menghapus SPT ini?"
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mb-4">Belum ada SPT untuk Nota Dinas ini</p>
                
                @if($notaDinas && $notaDinas->status === 'APPROVED')
                    <button 
                        wire:click="createSpt({{ $notaDinasId }})"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Buat SPT
                    </button>
                @else
                    <div class="text-sm text-gray-400 dark:text-gray-500">
                        <p>Nota Dinas harus disetujui terlebih dahulu untuk membuat SPT</p>
                        @if($notaDinas)
                            <p class="mt-1">Status saat ini: 
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $notaDinas->status === 'APPROVED' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                    {{ $notaDinas->status === 'APPROVED' ? 'Disetujui' : 'Draft' }}
                                </span>
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    @else
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p>Pilih Nota Dinas untuk melihat SPT</p>
        </div>
    @endif
</div>

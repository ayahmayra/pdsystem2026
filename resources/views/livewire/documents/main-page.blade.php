<div>
    <div class="min-h-screen dark:bg-gray-900">
        <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Perjadin / Dokumen
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        Kelola dokumen perjalanan dinas secara terintegrasi
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if(\App\Helpers\PermissionHelper::can('nota-dinas.create'))
                    <flux:button 
                        :href="route('nota-dinas.create')"
                        icon="plus"
                        variant="primary"
                    >
                        Buat Nota Dinas
                    </flux:button>
                        @endif
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 mb-6 w-full">
            <!-- Nota Dinas List (Master) -->
            <div class="col-span-1 w-full mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 w-full">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-blue-200 dark:bg-blue-800">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Nota Dinas
                        </h2>
                    </div>
                    <div class="p-0">
                        @livewire('documents.nota-dinas-list', ['selectedNotaDinasId' => $selectedNotaDinasId])
                    </div>
                </div>
            </div>

            <!-- SPT Table (Child) - Only show if Nota Dinas is selected -->
            @if($selectedNotaDinasId)
                <div class="col-span-1 w-full mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 w-full">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-purple-200 dark:bg-purple-800">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Surat Perintah Tugas (SPT)
                            </h2>
                        </div>
                        <div class="p-0">
                            @if($selectedNotaDinasId)
                                @livewire('documents.spt-table', ['notaDinasId' => $selectedNotaDinasId], key('spt-' . $selectedNotaDinasId))
                            @else
                                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Nota Dinas</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Klik pada Nota Dinas untuk melihat SPT terkait</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SPPD Table (Child) - Only show if SPT is selected -->
                @if($selectedSptId)
                    <div class="col-span-1 w-full mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 w-full">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-green-200 dark:bg-green-800">
                                <div class="flex justify-between items-center">
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        Surat Perintah Perjalanan Dinas (SPPD)
                                    </h2>
                                </div>
                            </div>
                            <div class="p-0">
                                @if($selectedSptId)
                                    @livewire('documents.sppd-table', ['sptId' => $selectedSptId], key('sppd-' . $selectedSptId))
                                @else
                                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Pilih SPT</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Klik pada SPT untuk melihat SPPD terkait</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Placeholder when no SPT selected -->
                    <div class="col-span-1 w-full mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 w-full">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Surat Perintah Perjalanan Dinas (SPPD)
                                </h2>
                            </div>
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p>Pilih SPT untuk melihat SPPD</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Kwitansi Section - Only show if SPPD is selected -->
                @if($selectedSppdId && $selectedSppd)
                    <div class="col-span-1 w-full mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 w-full">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-orange-200 dark:bg-orange-800">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Kwitansi
                                </h2>
                            </div>
                            <div class="p-4">
                                <div class="space-y-3">
                                    <!-- Kwitansi Status -->
                                    @php
                                        $receipts = $selectedSppdId ? \App\Models\Receipt::where('sppd_id', $selectedSppdId)->get() : collect();
                                    @endphp
                                    
                                    @if($receipts->count() > 0)
                                        <!-- Kwitansi Table -->
                                        <div class="overflow-x-auto w-full">
                                            <table class="w-full min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-800">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                            Nomor & Tanggal
                                                        </th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                            Penerima Pembayaran
                                                        </th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                            Bendahara
                                                        </th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                            Jumlah
                                                        </th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                            Status
                                                        </th>
                                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                            Aksi
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                                    @foreach($receipts as $receipt)
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                    {{ $receipt->receipt_no ?? 'Kwitansi Manual' }}
                                                                </div>
                                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                                    {{ $receipt->receipt_date ? \Carbon\Carbon::parse($receipt->receipt_date)->format('d/m/Y') : '-' }}
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                    @php
                                                                        $payeeSnapshot = $receipt->getPayeeUserSnapshot();
                                                                        $payeeName = $payeeSnapshot['name'] ?? 'N/A';
                                                                        $payeeGelarDepan = $payeeSnapshot['gelar_depan'] ?? '';
                                                                        $payeeGelarBelakang = $payeeSnapshot['gelar_belakang'] ?? '';
                                                                        
                                                                        $fullName = '';
                                                                        if ($payeeGelarDepan) {
                                                                            $fullName .= $payeeGelarDepan . ' ';
                                                                        }
                                                                        $fullName .= $payeeName;
                                                                        if ($payeeGelarBelakang) {
                                                                            $fullName .= ', ' . $payeeGelarBelakang;
                                                                        }
                                                                    @endphp
                                                                    {{ $fullName }}
                                                                </div>
                                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                                    {{ $payeeSnapshot['nip'] ?? 'N/A' }}
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                    @php
                                                                        $treasurerSnapshot = $receipt->getTreasurerUserSnapshot();
                                                                        $treasurerName = $treasurerSnapshot['name'] ?? 'N/A';
                                                                        $treasurerGelarDepan = $treasurerSnapshot['gelar_depan'] ?? '';
                                                                        $treasurerGelarBelakang = $treasurerSnapshot['gelar_belakang'] ?? '';
                                                                        
                                                                        $fullName = '';
                                                                        if ($treasurerGelarDepan) {
                                                                            $fullName .= $treasurerGelarDepan . ' ';
                                                                        }
                                                                        $fullName .= $treasurerName;
                                                                        if ($treasurerGelarBelakang) {
                                                                            $fullName .= ', ' . $treasurerGelarBelakang;
                                                                        }
                                                                    @endphp
                                                                    {{ $fullName }}
                                                                </div>
                                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                                    {{ $receipt->treasurer_title ?? 'N/A' }}
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                    Rp {{ number_format($receipt->total_amount, 0, ',', '.') }}
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                                    @if($receipt->status === 'DRAFT') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                                    @elseif($receipt->status === 'APPROVED') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                                                    {{ $receipt->status }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                                <flux:dropdown position="bottom" align="end">
                                                                    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                                                    <flux:menu>
                                                                        <!-- Cetak -->
                                                                        <flux:menu.item 
                                                                            href="{{ route('receipts.pdf', $receipt) }}" 
                                                                            target="_blank"
                                                                            icon="printer"
                                                                        >
                                                                            Cetak
                                                                        </flux:menu.item>

                                                                        <!-- Edit -->
                                                                        <flux:menu.item 
                                                                            href="{{ route('receipts.edit', $receipt) }}"
                                                                            icon="pencil-square"
                                                                        >
                                                                            Edit
                                                                        </flux:menu.item>

                                                                        <!-- Separator -->
                                                                        <flux:menu.separator />

                                                                        <!-- Delete -->
                                                                        <flux:menu.item 
                                                                            wire:click="deleteReceipt({{ $receipt->id }})"
                                                                            wire:confirm="Apakah Anda yakin ingin menghapus kwitansi ini?"
                                                                            variant="danger"
                                                                            icon="trash"
                                                                        >
                                                                            Hapus
                                                                        </flux:menu.item>
                                                                    </flux:menu>
                                                                </flux:dropdown>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        @php
                                            // Check if there are still participants without receipts
                                            $allParticipants = $selectedSppd->spt->notaDinas->participants;
                                            $participantsWithReceipts = \App\Models\Receipt::where('sppd_id', $selectedSppdId)
                                                ->pluck('payee_user_id')
                                                ->toArray();
                                            $availableParticipants = $allParticipants->filter(function ($participant) use ($participantsWithReceipts) {
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
                                        
                                        @if($availableParticipants->count() > 0)
                                            <!-- Status Info for Additional Kwitansi -->
                                            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md mb-4 mt-4">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Masih Ada Peserta Tanpa Kwitansi</span>
                                                </div>
                                                <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                                                    {{ $availableParticipants->count() }} peserta masih belum dibuatkan kwitansi
                                                </p>
                                            </div>

                                            <!-- Peserta yang Belum Memiliki Kwitansi -->
                                            <div class="mb-4">
                                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Peserta yang Belum Memiliki Kwitansi:</h4>
                                                <div class="space-y-1">
                                                    @foreach($availableParticipants->take(3) as $participant)
                                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                                            • {{ $participant->user?->fullNameWithTitles() ?? 'N/A' }} ({{ $participant->user?->position?->name ?? 'N/A' }})
                                                        </div>
                                                    @endforeach
                                                    @if($availableParticipants->count() > 3)
                                                        <div class="text-xs text-gray-500 dark:text-gray-500">
                                                            ... dan {{ $availableParticipants->count() - 3 }} peserta lainnya
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Action Button -->
                                            <button 
                                                wire:click="createKwitansi"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Buat Kwitansi
                                            </button>
                                        @else
                                            <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md mt-4">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="text-sm font-medium text-green-800 dark:text-green-200">Semua Peserta Sudah Memiliki Kwitansi</span>
                                                </div>
                                                <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                                    Kwitansi untuk semua peserta SPPD ini sudah dibuat
                                                </p>
                                            </div>
                                        @endif
                                    @else
                                        @php
                                            // Check if the selected SPPD has receipts
                                            $sppdHasReceipts = $selectedSppd ? $selectedSppd->receipts()->exists() : false;
                                        @endphp
                                        
                                        @if(!$selectedSppd)
                                            <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    <span class="text-sm font-medium text-red-800 dark:text-red-200">SPPD Belum Dibuat</span>
                                                </div>
                                                <p class="text-xs text-red-700 dark:text-red-300 mt-1">Buat SPPD terlebih dahulu sebelum membuat kwitansi</p>
                                            </div>
                                        @elseif($sppdHasReceipts)
                                            <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="text-sm font-medium text-green-800 dark:text-green-200">SPPD Sudah Memiliki Kwitansi</span>
                                                </div>
                                                <p class="text-xs text-green-700 dark:text-green-300 mt-1">Kwitansi untuk SPPD ini sudah dibuat</p>
                                            </div>
                                        @else
                                            @php
                                                // Get all participants from Nota Dinas
                                                $allParticipants = $selectedSppd->spt->notaDinas->participants;
                                                
                                                // Get participants who already have receipts for this SPPD
                                                $participantsWithReceipts = \App\Models\Receipt::where('sppd_id', $selectedSppdId)
                                                    ->pluck('payee_user_id')
                                                    ->toArray();
                                                
                                                // Get available participants and sort them
                                                $availableParticipants = $allParticipants->filter(function ($participant) use ($participantsWithReceipts) {
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
                                            
                                            @if($availableParticipants->count() > 0)
                                                <!-- Status Info -->
                                                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md mb-4">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                        </svg>
                                                        <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Kwitansi Belum Dibuat</span>
                                                    </div>
                                                    <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                                                        {{ $availableParticipants->count() }} peserta tersedia untuk dibuatkan kwitansi
                                                    </p>
                                                </div>

                                                <!-- Peserta yang Belum Memiliki Kwitansi -->
                                                <div class="mb-4">
                                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Peserta yang Belum Memiliki Kwitansi:</h4>
                                                    <div class="space-y-1">
                                                        @foreach($availableParticipants->take(3) as $participant)
                                                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                                                • {{ $participant->user?->fullNameWithTitles() ?? 'N/A' }} ({{ $participant->user?->position?->name ?? 'N/A' }})
                                                            </div>
                                                        @endforeach
                                                        @if($availableParticipants->count() > 3)
                                                            <div class="text-xs text-gray-500 dark:text-gray-500">
                                                                ... dan {{ $availableParticipants->count() - 3 }} peserta lainnya
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <!-- Action Button -->
                                                <button 
                                                    wire:click="createKwitansi"
                                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                                >
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    Buat Kwitansi
                                                </button>
                                            @else
                                                <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <span class="text-sm font-medium text-green-800 dark:text-green-200">Semua Peserta Sudah Memiliki Kwitansi</span>
                                                    </div>
                                                    <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                                        Kwitansi untuk semua peserta SPPD ini sudah dibuat
                                                    </p>
                                                </div>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Placeholder when no SPPD selected for Kwitansi -->
                    <div class="col-span-1 w-full mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 w-full">
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <p>Pilih SPPD untuk mengelola kwitansi</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Laporan Perjalanan Dinas Section - Only show if SPT is selected -->
                @if($selectedSptId && $selectedSpt)
                    <div class="col-span-1 w-full mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-blue-200 dark:bg-blue-800">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Laporan Perjalanan Dinas
                                </h2>
                            </div>
                            <div class="p-0">
                                @php
                                    $tripReport = $selectedSptId ? \App\Models\TripReport::where('spt_id', $selectedSptId)->first() : null;
                                @endphp
                                @if($tripReport)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-800">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nomor & Tanggal</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ringkasan</th>
                                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $tripReport->doc_no ?? '-' }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $tripReport->report_date ? \Carbon\Carbon::parse($tripReport->report_date)->format('d/m/Y') : '-' }}</div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">{!! Str::limit(formatActivitiesForPdf($tripReport->activities), 120, '...') !!}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <flux:dropdown position="bottom" align="end">
                                                        <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                                        <flux:menu>
                                                            <!-- Cetak -->
                                                            <flux:menu.item 
                                                                href="{{ route('trip-reports.pdf', $tripReport) }}" 
                                                                target="_blank"
                                                                icon="printer"
                                                            >
                                                                Cetak
                                                            </flux:menu.item>

                                                            <!-- Edit -->
                                                            <flux:menu.item 
                                                                href="{{ route('trip-reports.edit', $tripReport) }}"
                                                                icon="pencil-square"
                                                            >
                                                                Edit
                                                            </flux:menu.item>

                                                            <!-- Separator -->
                                                            <flux:menu.separator />

                                                            <!-- Delete -->
                                                            <flux:menu.item 
                                                                wire:click="deleteTripReport({{ $tripReport->id }})"
                                                                wire:confirm="Apakah Anda yakin ingin menghapus laporan perjalanan dinas ini?"
                                                                variant="danger"
                                                                icon="trash"
                                                            >
                                                                Hapus
                                                            </flux:menu.item>
                                                        </flux:menu>
                                                    </flux:dropdown>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                    <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md m-4">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                                            <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Laporan Belum Dibuat</span>
                                        </div>
                                        <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">Buat laporan perjalanan dinas untuk SPT ini</p>
                                        <button wire:click="createLaporanPd" class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                            Buat Laporan Perjalanan Dinas
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Placeholder when no SPT selected for Laporan -->
                    <div class="col-span-1 w-full mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p>Pilih SPT untuk mengelola laporan</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Dokumen Pendukung Section - Only show if Nota Dinas is selected -->
                @if($selectedNotaDinasId && $selectedNotaDinas)
                    <div class="col-span-1 w-full mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-green-200 dark:bg-green-800">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Dokumen Pendukung
                                </h2>
                            </div>
                            <div class="p-0">
                                @php
                                    $supportingDocuments = \App\Models\SupportingDocument::where('nota_dinas_id', $selectedNotaDinasId)->get();
                                @endphp
                                @if($supportingDocuments->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-800">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Judul & Tipe</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Diunggah Oleh</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Waktu</th>
                                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($supportingDocuments as $doc)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $doc->title }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $doc->document_type }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $doc->uploader?->fullNameWithTitles() ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $doc->created_at?->format('d/m/Y H:i') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <flux:dropdown position="bottom" align="end">
                                                        <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                                        <flux:menu>
                                                            <!-- Unduh -->
                                                            <flux:menu.item 
                                                                href="{{ $doc->file_url }}" 
                                                                target="_blank"
                                                                icon="arrow-down-tray"
                                                            >
                                                                Unduh
                                                            </flux:menu.item>

                                                            <!-- Edit -->
                                                            <flux:menu.item 
                                                                href="{{ route('supporting-documents.edit', ['notaDinas' => $selectedNotaDinasId, 'document' => $doc->id]) }}"
                                                                icon="pencil-square"
                                                            >
                                                                Edit
                                                            </flux:menu.item>

                                                            <!-- Separator -->
                                                            <flux:menu.separator />

                                                            <!-- Delete -->
                                                            <flux:menu.item 
                                                                wire:click="deleteSupportingDocument({{ $doc->id }})"
                                                                wire:confirm="Apakah Anda yakin ingin menghapus dokumen '{{ $doc->title }}'?"
                                                                variant="danger"
                                                                icon="trash"
                                                            >
                                                                Hapus
                                                            </flux:menu.item>
                                                        </flux:menu>
                                                    </flux:dropdown>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                    <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md m-4">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                                            <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Belum Ada Dokumen</span>
                                        </div>
                                        <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">Upload dokumen pendukung untuk Nota Dinas ini</p>
                                    </div>
                                @endif
                                <div class="m-4">
                                    <button onclick="window.location.href='{{ route('supporting-documents.upload', $selectedNotaDinasId) }}'" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        Upload Dokumen Pendukung
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Placeholder when no Nota Dinas selected for Dokumen Pendukung -->
                    <div class="col-span-1 w-full mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p>Pilih Nota Dinas untuk mengelola dokumen pendukung</p>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- Placeholder when no Nota Dinas selected -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-medium mb-2">Pilih Nota Dinas</p>
                            <p class="text-sm">Pilih Nota Dinas dari daftar di sebelah kiri untuk melihat SPT dan SPPD terkait</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

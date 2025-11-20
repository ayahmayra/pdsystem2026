<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900 dark:border-green-700 dark:text-green-300">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('sub-keg.index') }}" 
                           class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali ke Sub Kegiatan
                        </a>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Rekening Belanja</h1>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $subKeg->kode_subkeg }} - {{ $subKeg->nama_subkeg }}
                            </p>
                        </div>
                    </div>
                    <button 
                        wire:click="createRekening"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Rekening
                    </button>
                </div>

                <!-- Sub Kegiatan Info -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium text-blue-800 dark:text-blue-200">Kode Sub Kegiatan</label>
                            <p class="text-sm text-blue-900 dark:text-blue-100">{{ $subKeg->kode_subkeg }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-blue-800 dark:text-blue-200">Nama Sub Kegiatan</label>
                            <p class="text-sm text-blue-900 dark:text-blue-100">{{ $subKeg->nama_subkeg }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-blue-800 dark:text-blue-200">Unit</label>
                            <p class="text-sm text-blue-900 dark:text-blue-100">
                                {{ $subKeg->unit ? $subKeg->unit->name : 'Belum ditentukan' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="flex flex-col sm:flex-row gap-4 mb-6">
                    <div class="flex-1">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Cari kode rekening atau nama rekening..."
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Kode Rekening
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nama Rekening
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pagu Anggaran
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Realisasi
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Sisa
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Keterangan
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($rekeningBelanja as $rekening)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $rekening->kode_rekening }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $rekening->nama_rekening }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $rekening->formatted_pagu }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $rekening->formatted_total_realisasi }}
                                            </div>
                                            @if($rekening->total_realisasi > 0)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $rekening->receipts->count() }} kwitansi
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-medium {{ $rekening->sisa_anggaran > 0 ? 'text-green-600 dark:text-green-400' : ($rekening->sisa_anggaran < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white') }}">
                                                {{ $rekening->formatted_sisa_anggaran }}
                                            </div>
                                            @if($rekening->sisa_anggaran < 0)
                                                <div class="text-xs text-red-500 dark:text-red-400">
                                                    Over budget
                                                </div>
                                            @elseif($rekening->sisa_anggaran > 0)
                                                <div class="text-xs text-green-500 dark:text-green-400">
                                                    Tersisa
                                                </div>
                                            @else
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Habis
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $rekening->keterangan ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($rekening->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <button 
                                                wire:click="editRekening({{ $rekening->id }})"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1 rounded"
                                                title="Edit Rekening"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="text-lg font-medium">Tidak ada rekening belanja</p>
                                                <p class="text-sm">Belum ada rekening belanja yang terkait dengan sub kegiatan ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($rekeningBelanja->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $rekeningBelanja->links() }}
                        </div>
                    @endif
                </div>

                <!-- Summary -->
                @if($rekeningBelanja->count() > 0)
                    <div class="mt-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $rekeningBelanja->count() }}</span>
                                rekening belanja ditemukan
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Total Pagu: 
                                <span class="font-medium text-gray-900 dark:text-white">
                                    Rp {{ number_format($rekeningBelanja->sum('pagu'), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Modal Create/Edit Rekening -->
                @if($showModal)
                    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModal">
                        <div class="relative top-10 mx-auto p-0 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800" wire:click.stop>
                            <!-- Header -->
                            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $isEditing ? 'Edit Rekening Belanja' : 'Tambah Rekening Belanja' }}
                                </h3>
                                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Form -->
                            <div class="p-6">
                                <form wire:submit.prevent="{{ $isEditing ? 'updateRekening' : 'storeRekening' }}" class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Kode Rekening -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Kode Rekening *
                                            </label>
                                            <input 
                                                type="text" 
                                                wire:model="form.kode_rekening" 
                                                placeholder="Contoh: 5.1.02.01.01.0001"
                                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white font-mono"
                                            />
                                            @error('form.kode_rekening') 
                                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                                            @enderror
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Kode rekening sesuai dengan standar akuntansi
                                            </p>
                                        </div>

                                        <!-- Nama Rekening -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Nama Rekening *
                                            </label>
                                            <input 
                                                type="text" 
                                                wire:model="form.nama_rekening" 
                                                placeholder="Contoh: Belanja Pegawai"
                                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                            />
                                            @error('form.nama_rekening') 
                                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                                            @enderror
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Nama lengkap rekening belanja
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Pagu Anggaran -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Pagu Anggaran
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 dark:text-gray-400 text-sm">Rp</span>
                                            </div>
                                            <input 
                                                type="number" 
                                                wire:model="form.pagu" 
                                                placeholder="0"
                                                min="0"
                                                step="0.01"
                                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                            />
                                        </div>
                                        @error('form.pagu') 
                                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                                        @enderror
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Pagu anggaran untuk rekening belanja ini (opsional)
                                        </p>
                                    </div>

                                    <!-- Keterangan -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Keterangan
                                        </label>
                                        <textarea 
                                            wire:model="form.keterangan" 
                                            rows="3"
                                            placeholder="Masukkan keterangan tambahan (opsional)"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                        ></textarea>
                                        @error('form.keterangan') 
                                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                                        @enderror
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Keterangan tambahan untuk rekening belanja ini (opsional)
                                        </p>
                                    </div>

                                    <!-- Info Format -->
                                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2">Keterangan Format:</h4>
                                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                            <p><strong>Kode Rekening:</strong> Mengikuti format standar akuntansi (X.X.XX.XX.XX.XXXX)</p>
                                            <p><strong>Nama Rekening:</strong> Deskripsi lengkap jenis belanja</p>
                                            <p><strong>Pagu Anggaran:</strong> Jumlah anggaran yang dialokasikan (opsional)</p>
                                            <p><strong>Keterangan:</strong> Informasi tambahan terkait rekening (opsional)</p>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                                        <button 
                                            type="button" 
                                            wire:click="closeModal"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white dark:border-gray-500 dark:hover:bg-gray-700"
                                        >
                                            Batal
                                        </button>
                                        <button 
                                            type="submit"
                                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            {{ $isEditing ? 'Update Rekening Belanja' : 'Simpan Rekening Belanja' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
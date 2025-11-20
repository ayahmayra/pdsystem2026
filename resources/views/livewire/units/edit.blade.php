<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('master-data.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Master Data
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Data Unit</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Edit data unit: {{ $unit->fullName() }}</p>
            </div>
        </div>
        <a href="{{ route('units.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Data Unit
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-6">
            <form wire:submit="update" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kode Unit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Kode Unit *
                        </label>
                        <input 
                            type="text" 
                            wire:model="code" 
                            placeholder="Contoh: BPKAD-KEU"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white font-mono"
                        />
                        @error('code') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Kode unik untuk unit (max 20 karakter)
                        </p>
                    </div>

                    <!-- Parent Unit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Parent Unit
                        </label>
                        <select 
                            wire:model="parent_id"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="">Unit Utama (Tidak ada parent)</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->fullName() }}</option>
                            @endforeach
                        </select>
                        @error('parent_id') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Pilih parent unit untuk membuat hierarki
                        </p>
                    </div>
                </div>

                <!-- Nama Unit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nama Unit *
                    </label>
                    <input 
                        type="text" 
                        wire:model="name" 
                        placeholder="Contoh: Bidang Perbendaharaan"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    />
                    @error('name') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Contoh Data -->
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Contoh Struktur Unit:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong class="text-blue-700 dark:text-blue-300">Unit Utama:</strong>
                            <ul class="text-blue-600 dark:text-blue-400 ml-4">
                                <li>• BPKAD - Badan Pengelolaan Keuangan dan Aset Daerah</li>
                                <li>• SETDA - Sekretariat Daerah</li>
                                <li>• DISKOMINFO - Dinas Komunikasi dan Informatika</li>
                            </ul>
                        </div>
                        <div>
                            <strong class="text-blue-700 dark:text-blue-300">Sub Unit:</strong>
                            <ul class="text-blue-600 dark:text-blue-400 ml-4">
                                <li>• BPKAD-KEU - Bidang Perbendaharaan (Parent: BPKAD)</li>
                                <li>• BPKAD-ASET - Bidang Aset (Parent: BPKAD)</li>
                                <li>• SETDA-UMUM - Bagian Umum (Parent: SETDA)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Info Hierarki -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2">Keterangan Hierarki Unit:</h4>
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <p><strong>Unit Utama:</strong> Unit yang tidak memiliki parent (Dinas, Badan, Sekretariat)</p>
                        <p><strong>Sub Unit:</strong> Unit yang berada dibawah unit lain (Bidang, Bagian, Seksi)</p>
                        <p><strong>Kode Unit:</strong> Gunakan format singkat yang mudah diingat dan unik</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('units.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white dark:border-gray-500 dark:hover:bg-gray-700">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Data Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

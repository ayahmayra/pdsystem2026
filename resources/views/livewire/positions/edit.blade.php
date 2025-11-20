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
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Data Jabatan</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Edit data jabatan: {{ $position->fullName() }}</p>
            </div>
        </div>
        <a href="{{ route('positions.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Data Jabatan
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-6">
            <form wire:submit="update" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Jabatan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Jabatan *
                        </label>
                        <input 
                            type="text" 
                            wire:model="name" 
                            placeholder="Contoh: Kepala Bagian Keuangan"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        />
                        @error('name') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Tipe Jabatan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tipe Jabatan
                        </label>
                        <select 
                            wire:model="type"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="">-- Pilih Tipe Jabatan --</option>
                            <option value="Struktural">Struktural</option>
                            <option value="Fungsional">Fungsional</option>
                        </select>
                        @error('type') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Eselon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Eselon
                        </label>
                        <select 
                            wire:model="echelon_id"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="">Non Eselon</option>
                            @foreach($echelons as $echelon)
                                <option value="{{ $echelon->id }}">{{ $echelon->fullName() }}</option>
                            @endforeach
                        </select>
                        @error('echelon_id') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Pilih eselon untuk jabatan struktural, atau "Non Eselon" untuk jabatan fungsional
                        </p>
                    </div>
                </div>

                <!-- Contoh Data -->
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Contoh Data Jabatan:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong class="text-blue-700 dark:text-blue-300">Jabatan Struktural (Dengan Eselon):</strong>
                            <ul class="text-blue-600 dark:text-blue-400 ml-4">
                                <li>• Kepala Dinas (Eselon II.a)</li>
                                <li>• Sekretaris Dinas (Eselon III.a)</li>
                                <li>• Kepala Bidang (Eselon III.a)</li>
                                <li>• Kepala Bagian (Eselon IV.a)</li>
                                <li>• Kepala Sub Bagian (Eselon IV.b)</li>
                                <li>• Kepala Seksi (Eselon IV.b)</li>
                            </ul>
                        </div>
                        <div>
                            <strong class="text-blue-700 dark:text-blue-300">Jabatan Fungsional (Non Eselon):</strong>
                            <ul class="text-blue-600 dark:text-blue-400 ml-4">
                                <li>• Auditor (Non Eselon)</li>
                                <li>• Analis Kebijakan (Non Eselon)</li>
                                <li>• Pranata Komputer (Non Eselon)</li>
                                <li>• Perencana (Non Eselon)</li>
                                <li>• Bendahara (Non Eselon)</li>
                                <li>• Staff Administrasi (Non Eselon)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Info Tipe -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2">Keterangan Tipe Jabatan:</h4>
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <p><strong>Struktural:</strong> Jabatan dengan fungsi manajerial/kepemimpinan (Kepala, Sekretaris, dll)</p>
                        <p><strong>Fungsional:</strong> Jabatan berdasarkan keahlian teknis tertentu (Auditor, Analis, dll)</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('positions.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white dark:border-gray-500 dark:hover:bg-gray-700">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Data Jabatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

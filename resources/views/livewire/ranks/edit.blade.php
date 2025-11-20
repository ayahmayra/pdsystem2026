<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Data Pangkat</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Edit data pangkat: {{ $rank->fullName() }}</p>
        </div>
        <a href="{{ route('ranks.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-6">
            <form wire:submit="update" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kode Pangkat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Kode Pangkat *
                        </label>
                        <input 
                            type="text" 
                            wire:model="code" 
                            placeholder="Contoh: III/a, IV/b"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white font-mono"
                        />
                        @error('code') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Format: Golongan/Ruang (contoh: III/a untuk Penata Muda)
                        </p>
                    </div>

                    <!-- Nama Pangkat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Pangkat *
                        </label>
                        <input 
                            type="text" 
                            wire:model="name" 
                            placeholder="Contoh: Penata Muda"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        />
                        @error('name') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>

                <!-- Contoh Data -->
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Contoh Data Pangkat:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong class="text-blue-700 dark:text-blue-300">Golongan I:</strong>
                            <ul class="text-blue-600 dark:text-blue-400 ml-4">
                                <li>• I/a - Juru Muda</li>
                                <li>• I/b - Juru Muda Tingkat I</li>
                                <li>• I/c - Juru</li>
                                <li>• I/d - Juru Tingkat I</li>
                            </ul>
                        </div>
                        <div>
                            <strong class="text-blue-700 dark:text-blue-300">Golongan II:</strong>
                            <ul class="text-blue-600 dark:text-blue-400 ml-4">
                                <li>• II/a - Pengatur Muda</li>
                                <li>• II/b - Pengatur Muda Tingkat I</li>
                                <li>• II/c - Pengatur</li>
                                <li>• II/d - Pengatur Tingkat I</li>
                            </ul>
                        </div>
                        <div>
                            <strong class="text-blue-700 dark:text-blue-300">Golongan III:</strong>
                            <ul class="text-blue-600 dark:text-blue-400 ml-4">
                                <li>• III/a - Penata Muda</li>
                                <li>• III/b - Penata Muda Tingkat I</li>
                                <li>• III/c - Penata</li>
                                <li>• III/d - Penata Tingkat I</li>
                            </ul>
                        </div>
                        <div>
                            <strong class="text-blue-700 dark:text-blue-300">Golongan IV:</strong>
                            <ul class="text-blue-600 dark:text-blue-400 ml-4">
                                <li>• IV/a - Pembina</li>
                                <li>• IV/b - Pembina Tingkat I</li>
                                <li>• IV/c - Pembina Utama Muda</li>
                                <li>• IV/d - Pembina Utama Madya</li>
                                <li>• IV/e - Pembina Utama</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('ranks.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white dark:border-gray-500 dark:hover:bg-gray-700">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Data Pangkat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

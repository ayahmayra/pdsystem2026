<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit API Client</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Edit informasi API client</p>
        </div>
        <a href="{{ route('api-clients.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    @if (session('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900 dark:border-green-700 dark:text-green-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-6">
            <form wire:submit="save" class="space-y-6">
                <!-- Nama Aplikasi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nama Aplikasi *
                    </label>
                    <input 
                        type="text" 
                        wire:model="name" 
                        placeholder="Contoh: Aplikasi HRIS"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    />
                    @error('name') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Deskripsi
                    </label>
                    <textarea 
                        wire:model="description" 
                        rows="3"
                        placeholder="Deskripsi singkat tentang aplikasi..."
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    ></textarea>
                    @error('description') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- IP Whitelist -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        IP Whitelist (Opsional)
                    </label>
                    <input 
                        type="text" 
                        wire:model="ip_whitelist" 
                        placeholder="192.168.1.100,203.142.80.100"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white font-mono"
                    />
                    @error('ip_whitelist') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Daftar IP address yang diizinkan (pisahkan dengan koma). Kosongkan untuk mengizinkan semua IP.
                    </p>
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        wire:model="is_active"
                        id="is_active"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Aktifkan API client ini
                    </label>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4 rounded-lg">
                    <div class="flex">
                        <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Catatan:</strong> API key tidak dapat diubah atau dilihat setelah dibuat. Jika API key hilang atau terkompromi, buat API client baru dan nonaktifkan yang lama.
                        </div>
                    </div>
                </div>

                <!-- Statistik -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Request</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($apiClient->request_count) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Terakhir Digunakan</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ $apiClient->last_used_at ? $apiClient->last_used_at->format('d/m/Y H:i') : 'Belum pernah' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Dibuat</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ $apiClient->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('api-clients.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white dark:border-gray-500 dark:hover:bg-gray-700">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

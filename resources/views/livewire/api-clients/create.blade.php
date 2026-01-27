<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Tambah API Client</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Buat API client baru untuk aplikasi yang akan mengakses API referensi</p>
        </div>
        <a href="{{ route('api-clients.index') }}" 
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
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Nama aplikasi atau sistem yang akan menggunakan API key ini
                    </p>
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
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4 rounded-lg">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-yellow-800 dark:text-yellow-200">
                            <strong>Penting:</strong> API key akan ditampilkan sekali setelah dibuat. Pastikan untuk menyimpannya dengan aman karena tidak dapat dilihat lagi setelahnya.
                        </div>
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
                        Buat API Client
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- API Key Modal -->
    @if($generatedApiKey || session('api_key'))
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="api-key-modal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">API Key Berhasil Dibuat!</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4 rounded-lg mb-4">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200 font-medium mb-2">
                            ⚠️ Simpan API key ini dengan aman!
                        </p>
                        <p class="text-xs text-yellow-700 dark:text-yellow-300">
                            API key hanya ditampilkan sekali dan tidak dapat dilihat lagi setelah modal ini ditutup.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            API Key:
                        </label>
                        <div class="flex items-center space-x-2">
                            <input 
                                type="text" 
                                value="{{ $generatedApiKey ?? session('api_key') }}" 
                                id="api-key-input"
                                readonly
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono text-sm"
                            />
                            <button 
                                type="button"
                                onclick="copyApiKey()"
                                class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
                            >
                                Salin
                            </button>
                        </div>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg mb-4">
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Cara Menggunakan:</p>
                        <div class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                            <p><strong>Header:</strong> <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">X-API-Key: {{ \Illuminate\Support\Str::limit($generatedApiKey ?? session('api_key'), 30) }}...</code></p>
                            <p><strong>Query:</strong> <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">?api_key=...</code></p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button 
                            wire:click="closeModal"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        >
                            Saya Sudah Menyimpan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function copyApiKey() {
                const input = document.getElementById('api-key-input');
                input.select();
                input.setSelectionRange(0, 99999); // For mobile devices
                document.execCommand('copy');
                
                // Show feedback
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Tersalin!';
                button.classList.add('bg-green-600');
                button.classList.remove('bg-blue-600');
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('bg-green-600');
                    button.classList.add('bg-blue-600');
                }, 2000);
            }
        </script>
    @endif
</div>

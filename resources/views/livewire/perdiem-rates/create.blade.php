<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('reference-rates.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Referensi Tarif
                    </a>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Tambah Tarif Uang Harian</h1>
                </div>
                <a href="{{ route('perdiem-rates.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Kembali ke Daftar
                </a>
            </div>

            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <form wire:submit="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Province -->
                        <div>
                            <label for="province_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Provinsi <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="province_id" id="province_id" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Pilih provinsi</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->full_name }}</option>
                                @endforeach
                            </select>
                            @error('province_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>



                        <!-- Satuan -->
                        <div>
                            <label for="satuan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Satuan <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="satuan" type="text" id="satuan" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                   placeholder="Contoh: OH, Hari">
                            @error('satuan')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Luar Kota -->
                        <div>
                            <label for="luar_kota" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tarif Luar Kota (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="luar_kota" type="number" id="luar_kota" step="1000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                   placeholder="Contoh: 1500000">
                            @error('luar_kota')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dalam Kota >8h -->
                        <div>
                            <label for="dalam_kota_gt8h" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tarif Dalam Kota >8 Jam (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="dalam_kota_gt8h" type="number" id="dalam_kota_gt8h" step="1000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                   placeholder="Contoh: 750000">
                            @error('dalam_kota_gt8h')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Diklat -->
                        <div>
                            <label for="diklat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tarif Diklat (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="diklat" type="number" id="diklat" step="1000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                   placeholder="Contoh: 1000000">
                            @error('diklat')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('perdiem-rates.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Batal
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

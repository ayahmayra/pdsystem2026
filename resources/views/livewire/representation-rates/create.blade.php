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
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Tambah Tarif Representasi</h1>
                </div>
                <a href="{{ route('representation-rates.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
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
                        <!-- Travel Grade -->
                        <div>
                            <label for="travel_grade_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tingkatan Perjalanan <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="travel_grade_id" id="travel_grade_id" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Pilih tingkatan</option>
                                @foreach($travelGrades as $grade)
                                    <option value="{{ $grade->id }}">{{ $grade->display_name }}</option>
                                @endforeach
                            </select>
                            @error('travel_grade_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @if($travelGrades->isEmpty())
                                <p class="mt-1 text-sm text-yellow-600 dark:text-yellow-400">
                                    Semua tingkatan sudah memiliki tarif representasi
                                </p>
                            @endif
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
                                   placeholder="Contoh: 500000">
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
                                   placeholder="Contoh: 250000">
                            @error('dalam_kota_gt8h')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                    Informasi Tarif Representasi
                                </h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                    <p>Tarif representasi hanya berlaku untuk Bupati/Wakil Bupati/Pimpinan DPRD dan Anggota DPRD/Eselon II.</p>
                                    <p class="mt-1">Tarif ini digunakan untuk keperluan representasi dalam perjalanan dinas.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('representation-rates.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Batal
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @if($travelGrades->isEmpty()) disabled @endif>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

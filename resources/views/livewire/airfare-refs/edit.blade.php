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
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Referensi Tiket Pesawat</h1>
                </div>
                <a href="{{ route('airfare-refs.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Kembali ke Daftar
                </a>
            </div>

            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <form wire:submit="update">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Origin City -->
                        <div>
                            <label for="origin_city_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Kota Asal <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="origin_city_id" id="origin_city_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Pilih kota asal</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->display_name }} - {{ $city->province->name }}</option>
                                @endforeach
                            </select>
                            @error('origin_city_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Destination City -->
                        <div>
                            <label for="destination_city_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Kota Tujuan <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="destination_city_id" id="destination_city_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Pilih kota tujuan</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->display_name }} - {{ $city->province->name }}</option>
                                @endforeach
                            </select>
                            @error('destination_city_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Class -->
                        <div>
                            <label for="class" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Kelas <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="class" id="class" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="ECONOMY">ECONOMY</option>
                                <option value="BUSINESS">BUSINESS</option>
                            </select>
                            @error('class')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- PP Estimate -->
                        <div>
                            <label for="pp_estimate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Estimasi per Orang (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="pp_estimate" type="number" id="pp_estimate" step="1000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                   placeholder="Contoh: 1500000">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Estimasi harga tiket per orang untuk RAB/anggaran
                            </p>
                            @error('pp_estimate')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('airfare-refs.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Batal
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Rute Perjalanan</h1>
                <a href="{{ route('travel-routes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
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
                        <!-- Origin Place -->
                        <div>
                            <label for="origin_place_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tempat Asal <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="origin_place_id" id="origin_place_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Pilih tempat asal</option>
                                @foreach($orgPlaces as $place)
                                    <option value="{{ $place->id }}">{{ $place->display_name }}</option>
                                @endforeach
                            </select>
                            @error('origin_place_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Destination Place -->
                        <div>
                            <label for="destination_place_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tempat Tujuan <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="destination_place_id" id="destination_place_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Pilih tempat tujuan</option>
                                @foreach($orgPlaces as $place)
                                    <option value="{{ $place->id }}">{{ $place->display_name }}</option>
                                @endforeach
                            </select>
                            @error('destination_place_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Transport Mode -->
                        <div>
                            <label for="mode_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Moda Transportasi <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="mode_id" id="mode_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Pilih moda transportasi</option>
                                @foreach($transportModes as $mode)
                                    <option value="{{ $mode->id }}">{{ $mode->display_name }}</option>
                                @endforeach
                            </select>
                            @error('mode_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Class (only for AIR mode) -->
                        <div>
                            <label for="class" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Kelas (Hanya untuk Pesawat)
                            </label>
                            <select wire:model.live="class" id="class" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    @if(!$mode_id || !$airMode || $mode_id != $airMode->id) disabled @endif>
                                <option value="">Pilih kelas</option>
                                <option value="ECONOMY">Economy</option>
                                <option value="BUSINESS">Business</option>
                            </select>
                            @error('class')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Round Trip -->
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input wire:model="is_roundtrip" type="checkbox" id="is_roundtrip" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_roundtrip" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Pulang Pergi
                                </label>
                            </div>
                            @error('is_roundtrip')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('travel-routes.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
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

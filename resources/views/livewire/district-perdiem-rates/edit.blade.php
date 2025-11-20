<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex items-center mb-6">
                <a href="{{ route('district-perdiem-rates.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    ← Kembali ke Tarif Uang Harian Kecamatan
                </a>
                <h2 class="text-2xl font-bold text-gray-900">Edit Tarif Uang Harian Kecamatan</h2>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form wire:submit="update">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Kedudukan -->
                            <div>
                                <label for="org_place_name" class="block text-sm font-medium text-gray-700">
                                    Kedudukan <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="org_place_name" type="text" id="org_place_name" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="Contoh: BENGKALIS, DURI">
                                @error('org_place_name') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kecamatan -->
                            <div>
                                <label for="district_id" class="block text-sm font-medium text-gray-700">
                                    Kecamatan <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="district_id" id="district_id" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Pilih Kecamatan</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                                @error('district_id') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Satuan -->
                            <div>
                                <label for="unit" class="block text-sm font-medium text-gray-700">
                                    Satuan <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="unit" type="text" id="unit" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="Contoh: OH (Orang/Hari)">
                                @error('unit') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Uang Harian -->
                            <div>
                                <label for="daily_rate" class="block text-sm font-medium text-gray-700">
                                    Uang Harian (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="daily_rate" type="number" id="daily_rate" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="Contoh: 150000">
                                @error('daily_rate') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="sm:col-span-2">
                                <div class="flex items-center">
                                    <input wire:model="is_active" type="checkbox" id="is_active" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        Aktif
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-3">
                            <a href="{{ route('district-perdiem-rates.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

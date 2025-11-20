<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Tambah Kedudukan Organisasi</h1>
                <a href="{{ route('org-places.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form wire:submit="save">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kedudukan</label>
                                <input type="text" wire:model="name" id="name" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                                       placeholder="Contoh: Kedudukan Bengkalis">
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="selected_province_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Provinsi</label>
                                <select wire:model.live="selected_province_id" id="selected_province_id" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="selected_city_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kota/Kabupaten</label>
                                <select wire:model.live="selected_city_id" id="selected_city_id" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                                        @if(!$selected_province_id) disabled @endif>
                                    <option value="">Pilih Kota/Kabupaten</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->display_name }}</option>
                                    @endforeach
                                </select>
                                @if(!$selected_province_id)
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih provinsi terlebih dahulu</p>
                                @endif
                            </div>

                            <div>
                                <label for="district_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kecamatan (Opsional)</label>
                                <select wire:model="district_id" id="district_id" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                                        @if(!$selected_city_id) disabled @endif>
                                    <option value="">Pilih Kecamatan (Opsional)</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                                @error('district_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                @if(!$selected_city_id)
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih kota/kabupaten terlebih dahulu</p>
                                @endif
                            </div>

                            <div class="sm:col-span-2">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="is_org_headquarter" id="is_org_headquarter" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                                    <label for="is_org_headquarter" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                        Kantor Pusat Organisasi
                                    </label>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Centang jika ini adalah kantor pusat organisasi
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-3">
                            <a href="{{ route('org-places.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
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
</div>

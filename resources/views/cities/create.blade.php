<x-layouts.app.sidebar title="Tambah Kota/Kabupaten">
    <flux:main>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Tambah Kota/Kabupaten</h1>
            <a href="{{ route('cities.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form method="POST" action="{{ route('cities.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="kemendagri_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Kemendagri</label>
                            <input type="text" name="kemendagri_code" id="kemendagri_code" 
                                   value="{{ old('kemendagri_code') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('kemendagri_code') border-red-500 @enderror"
                                   placeholder="Contoh: 1401">
                            @error('kemendagri_code') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kota/Kabupaten</label>
                            <input type="text" name="name" id="name" 
                                   value="{{ old('name') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('name') border-red-500 @enderror"
                                   placeholder="Contoh: BENGKALIS">
                            @error('name') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div>
                            <label for="province_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Provinsi</label>
                            <select name="province_id" id="province_id" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('province_id') border-red-500 @enderror">
                                <option value="">Pilih Provinsi</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('province_id') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe</label>
                            <select name="type" id="type" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('type') border-red-500 @enderror">
                                <option value="">Pilih Tipe</option>
                                <option value="KAB" {{ old('type') == 'KAB' ? 'selected' : '' }}>Kabupaten</option>
                                <option value="KOTA" {{ old('type') == 'KOTA' ? 'selected' : '' }}>Kota</option>
                            </select>
                            @error('type') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <a href="{{ route('cities.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
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
    </flux:main>
</x-layouts.app.sidebar>

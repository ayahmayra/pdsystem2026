<x-layouts.app.sidebar title="Tambah Jabatan">
    <flux:main>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('positions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Tambah Jabatan</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan jabatan baru ke sistem</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('positions.store') }}">
                @csrf
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Jabatan</label>
                        <input type="text" name="name" id="name" 
                               value="{{ old('name') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('name') border-red-500 @enderror"
                               placeholder="Contoh: Kepala Bagian Umum">
                        @error('name') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Jabatan</label>
                        <input type="text" name="type" id="type" 
                               value="{{ old('type') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('type') border-red-500 @enderror"
                               placeholder="Contoh: Struktural">
                        @error('type') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <label for="echelon_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Eselon</label>
                        <select name="echelon_id" id="echelon_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('echelon_id') border-red-500 @enderror">
                            <option value="">Pilih Eselon</option>
                            @foreach($echelons as $echelon)
                                <option value="{{ $echelon->id }}" {{ old('echelon_id') == $echelon->id ? 'selected' : '' }}>
                                    {{ $echelon->code }} - {{ $echelon->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('echelon_id') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('positions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
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
    </flux:main>
</x-layouts.app.sidebar>

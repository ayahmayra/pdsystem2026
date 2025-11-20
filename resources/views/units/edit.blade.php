<x-layouts.app.sidebar title="Edit Unit">
    <flux:main>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('units.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Unit</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Edit data unit/bidang/bagian</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('units.update', $unit) }}">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Unit</label>
                        <input type="text" name="code" id="code" 
                               value="{{ old('code', $unit->code) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('code') border-red-500 @enderror"
                               placeholder="Contoh: U001">
                        @error('code') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Unit</label>
                        <input type="text" name="name" id="name" 
                               value="{{ old('name', $unit->name) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('name') border-red-500 @enderror"
                               placeholder="Contoh: Bagian Umum">
                        @error('name') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Parent (Opsional)</label>
                        <select name="parent_id" id="parent_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('parent_id') border-red-500 @enderror">
                            <option value="">Pilih Unit Parent</option>
                            @foreach($units as $unitOption)
                                <option value="{{ $unitOption->id }}" {{ old('parent_id', $unit->parent_id) == $unitOption->id ? 'selected' : '' }}>
                                    {{ $unitOption->code }} - {{ $unitOption->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Unit parent akan menjadi bagian dari unit ini</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('units.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
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
    </flux:main>
</x-layouts.app.sidebar>

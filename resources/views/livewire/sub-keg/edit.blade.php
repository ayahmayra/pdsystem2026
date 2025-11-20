<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('master-data.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Master Data
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Data Sub Kegiatan</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Edit data sub kegiatan dan rekening belanja</p>
            </div>
        </div>
        <a href="{{ route('sub-keg.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Data Sub Kegiatan
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-6">
            <form wire:submit="update" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kode Sub Kegiatan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Kode Sub Kegiatan *
                        </label>
                        <input 
                            type="text" 
                            wire:model="kode_subkeg" 
                            placeholder="Contoh: 5.02.02.2.01.0001"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white font-mono"
                        />
                        @error('kode_subkeg') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Kode unik untuk sub kegiatan (format: X.XX.XX.X.XX.XXXX)
                        </p>
                    </div>

                    <!-- Unit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Unit *
                        </label>
                        <select 
                            wire:model="id_unit"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="">Pilih Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('id_unit') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Pilih unit yang bertanggung jawab atas sub kegiatan ini
                        </p>
                    </div>

                    <!-- PPTK (Pejabat Pelaksana Teknis Kegiatan) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            PPTK (Pejabat Pelaksana Teknis Kegiatan)
                        </label>
                        <select 
                            wire:model="pptk_user_id"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="">Pilih PPTK (Opsional)</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->fullNameWithTitles() }}
                                    @if($user->position)
                                        - {{ $user->position->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('pptk_user_id') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Pilih pejabat yang bertanggung jawab sebagai PPTK untuk sub kegiatan ini (opsional)
                        </p>
                    </div>
                </div>

                <!-- Nama Sub Kegiatan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nama Sub Kegiatan *
                    </label>
                    <input 
                        type="text" 
                        wire:model="nama_subkeg" 
                        placeholder="Contoh: Koordinasi dan Penyusunan KUA dan PPAS"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    />
                    @error('nama_subkeg') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>


                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('sub-keg.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white dark:border-gray-500 dark:hover:bg-gray-700">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Data Sub Kegiatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

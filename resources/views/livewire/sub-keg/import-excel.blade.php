<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Import Data Sub Kegiatan & Rekening Belanja
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Upload file Excel untuk mengimpor data Sub Kegiatan dan Rekening Belanja sekaligus
                        </p>
                    </div>
                    <a href="{{ route('sub-keg.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Kembali
                    </a>
                </div>

                <!-- Flash Messages -->
                @if (session()->has('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Upload Form -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Upload File Excel
                    </h3>

                    <form wire:submit="import" class="space-y-4">
                        <!-- File Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Pilih File Excel
                            </label>
                            <input type="file" 
                                   wire:model="excelFile" 
                                   accept=".xlsx,.xls,.csv"
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-full file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-blue-50 file:text-blue-700
                                          hover:file:bg-blue-100
                                          dark:file:bg-gray-600 dark:file:text-gray-300">
                            @error('excelFile') 
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Progress Bar -->
                        @if($isUploading)
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" 
                                     style="width: {{ $uploadProgress }}%"></div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Mengupload dan memproses file... {{ $uploadProgress }}%
                            </p>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-4">
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    wire:target="import"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                                <svg wire:loading.remove wire:target="import" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <svg wire:loading wire:target="import" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="import">Import Data</span>
                                <span wire:loading wire:target="import">Memproses...</span>
                            </button>

                            <button type="button" 
                                    wire:click="downloadTemplate"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Template
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Import Results -->
                @if($showResults && $importResults)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Hasil Import
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ $importResults['sub_keg_created'] }}
                                </div>
                                <div class="text-sm text-blue-800 dark:text-blue-200">
                                    Sub Kegiatan Baru
                                </div>
                            </div>

                            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                    {{ $importResults['sub_keg_updated'] }}
                                </div>
                                <div class="text-sm text-yellow-800 dark:text-yellow-200">
                                    Sub Kegiatan Diupdate
                                </div>
                            </div>

                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $importResults['rekening_belanja_created'] }}
                                </div>
                                <div class="text-sm text-green-800 dark:text-green-200">
                                    Rekening Belanja Baru
                                </div>
                            </div>

                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ $importResults['rekening_belanja_updated'] }}
                                </div>
                                <div class="text-sm text-blue-800 dark:text-blue-200">
                                    Rekening Belanja Diupdate
                                </div>
                            </div>
                        </div>

                        @if(!empty($importResults['errors']))
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">
                                    Error yang ditemukan:
                                </h4>
                                <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                                    @foreach($importResults['errors'] as $error)
                                        <li>• {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Format File Information -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-4">
                        Format File Excel
                    </h3>
                    
                    <div class="text-sm text-blue-700 dark:text-blue-300 space-y-2">
                        <p><strong>File yang didukung:</strong> .xlsx, .xls, .csv</p>
                        <p><strong>Ukuran maksimal:</strong> 10MB</p>
                        <p><strong>Baris pertama:</strong> Header kolom (akan diabaikan)</p>
                    </div>

                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">
                            Kolom yang diperlukan:
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-700 dark:text-blue-300">
                            <div>
                                <ul class="space-y-1">
                                    <li><strong>Kode Sub Kegiatan:</strong> Kode unik sub kegiatan</li>
                                    <li><strong>Nama Sub Kegiatan:</strong> Nama lengkap sub kegiatan</li>
                                </ul>
                            </div>
                            <div>
                                <ul class="space-y-1">
                                    <li><strong>Kode Rekening:</strong> Kode rekening belanja</li>
                                    <li><strong>Nama Rekening:</strong> Nama rekening belanja</li>
                                    <li><strong>Pagu:</strong> Nilai pagu (opsional)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-blue-100 dark:bg-blue-800/30 rounded">
                        <p class="text-xs text-blue-800 dark:text-blue-200">
                            <strong>Catatan:</strong> Jika Sub Kegiatan dengan kode yang sama sudah ada, 
                            sistem akan mengupdate nama sub kegiatan dan menambahkan rekening belanja baru. 
                            Rekening belanja dengan kode yang sama tidak akan diduplikasi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

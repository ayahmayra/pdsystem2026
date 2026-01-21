<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Data Pegawai
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Import Data Pegawai</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload file Excel untuk mengimport data pegawai secara massal</p>
            </div>
        </div>
    </div>

    @if (session('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900 dark:border-green-700 dark:text-green-300">
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg dark:bg-red-900 dark:border-red-700 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    <!-- Instructions -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
        <h2 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">Petunjuk Import</h2>
        <ul class="list-disc list-inside space-y-2 text-sm text-blue-800 dark:text-blue-200">
            <li>File Excel harus memiliki kolom: <strong>Nama</strong> (wajib), <strong>NIP</strong> (wajib), <strong>Jabatan</strong> (opsional), <strong>Bidang</strong> (opsional)</li>
            <li>Baris pertama adalah header (akan diabaikan)</li>
            <li><strong>Kolom wajib:</strong> Nama dan NIP harus diisi</li>
            <li><strong>Kolom opsional:</strong> Jabatan dan Bidang boleh dikosongkan</li>
            <li>Jika kolom <strong>Bidang</strong> dikosongkan, user akan dibuat tanpa unit (unit_id = null)</li>
            <li>Jika kolom <strong>Bidang</strong> diisi tetapi unit tidak ditemukan, akan dibuat unit baru secara otomatis</li>
            <li>Jika user dengan NIP yang sama sudah ada, data akan diupdate</li>
            <li>Email akan dibuat otomatis dari nama jika belum ada</li>
            <li>Password default untuk user baru: <strong>password123</strong></li>
        </ul>
        <div class="mt-4">
            <a href="{{ route('users.import.template') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download Template Excel
            </a>
        </div>
    </div>

    <!-- Import Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form wire:submit.prevent="import">
            <!-- Employee Type Selection -->
            <div class="mb-6">
                <label for="employee_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Jenis Pegawai <span class="text-red-500">*</span>
                </label>
                <select 
                    wire:model="employee_type" 
                    id="employee_type"
                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                    required
                >
                    <option value="PNS">PNS</option>
                    <option value="PPPK">PPPK</option>
                    <option value="PPPK PW">PPPK PW</option>
                    <option value="Non ASN">Non ASN</option>
                </select>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Semua pegawai yang diimport akan memiliki jenis pegawai yang dipilih
                </p>
                @error('employee_type')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div class="mb-6">
                <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    File Excel <span class="text-red-500">*</span>
                </label>
                <input 
                    type="file" 
                    wire:model="file" 
                    id="file"
                    accept=".xlsx,.xls,.csv"
                    class="block w-full text-sm text-gray-500 dark:text-gray-400
                           file:mr-4 file:py-2 file:px-4
                           file:rounded-md file:border-0
                           file:text-sm file:font-semibold
                           file:bg-blue-50 file:text-blue-700
                           hover:file:bg-blue-100
                           dark:file:bg-blue-900 dark:file:text-blue-300
                           dark:hover:file:bg-blue-800"
                >
                @error('file')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @if ($file)
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        File: {{ $file->getClientOriginalName() }} ({{ number_format($file->getSize() / 1024, 2) }} KB)
                    </p>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('users.index') }}" 
                   class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Batal
                </a>
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="import">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Import Data
                    </span>
                    <span wire:loading wire:target="import" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mengimport...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Import Results -->
    @if ($importResults)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Hasil Import</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $importResults['created'] }}</div>
                    <div class="text-sm text-green-700 dark:text-green-300">Data Dibuat</div>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $importResults['updated'] }}</div>
                    <div class="text-sm text-blue-700 dark:text-blue-300">Data Diupdate</div>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $importResults['skipped'] }}</div>
                    <div class="text-sm text-yellow-700 dark:text-yellow-300">Data Dilewati</div>
                </div>
            </div>

            @if (!empty($importResults['errors']))
                <div class="mt-4">
                    <h3 class="text-sm font-semibold text-red-600 dark:text-red-400 mb-2">Error Details:</h3>
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 max-h-60 overflow-y-auto">
                        <ul class="list-disc list-inside space-y-1 text-sm text-red-700 dark:text-red-300">
                            @foreach ($importResults['errors'] as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

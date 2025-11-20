<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex items-center mb-6">
                <a href="{{ $this->getBackUrl() }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    ← Kembali ke Dokumen
                </a>
                <h2 class="text-2xl font-bold text-gray-900">Edit Dokumen Pendukung</h2>
            </div>

            @if (session()->has('message'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Nota Dinas Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-medium text-blue-900 mb-2">Informasi Nota Dinas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-blue-800">Nomor Nota Dinas:</span>
                        <span class="text-blue-700">{{ $notaDinas->doc_no ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-blue-800">Tanggal:</span>
                        <span class="text-blue-700">{{ $notaDinas->nd_date ? \Carbon\Carbon::parse($notaDinas->nd_date)->format('d/m/Y') : '-' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-blue-800">Tempat Asal:</span>
                        <span class="text-blue-700">{{ $notaDinas->originPlace->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-blue-800">Tujuan:</span>
                        <span class="text-blue-700">{{ $notaDinas->destinationCity->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form wire:submit="update">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Jenis Dokumen -->
                            <div>
                                <label for="document_type" class="block text-sm font-medium text-gray-700">
                                    Jenis Dokumen <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="document_type" id="document_type" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Pilih jenis dokumen</option>
                                    <option value="Undangan">Undangan</option>
                                    <option value="Foto">Foto</option>
                                    <option value="Surat Tugas">Surat Tugas</option>
                                    <option value="Laporan">Laporan</option>
                                    <option value="Sertifikat">Sertifikat</option>
                                    <option value="Dokumen Lainnya">Dokumen Lainnya</option>
                                </select>
                                @error('document_type') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Judul Dokumen -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">
                                    Judul Dokumen <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="title" type="text" id="title" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="Masukkan judul dokumen">
                                @error('title') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Deskripsi
                                </label>
                                <textarea wire:model="description" id="description" rows="3" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                          placeholder="Deskripsi dokumen (opsional)"></textarea>
                            </div>

                            <!-- File Upload -->
                            <div>
                                <label for="file" class="block text-sm font-medium text-gray-700">
                                    File Dokumen
                                </label>
                                <div class="mt-1">
                                    @if($document->file_path)
                                        <div class="mb-3 p-3 bg-gray-50 border border-gray-300 rounded-md">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <span class="text-sm text-gray-700">{{ $document->file_name }}</span>
                                                </div>
                                                <a href="{{ $document->file_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                                    Lihat File
                                                </a>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Ukuran: {{ $document->file_size_human }} | 
                                                Upload: {{ $document->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    @endif
                                    <input wire:model="file" type="file" id="file" 
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="mt-1 text-sm text-gray-500">
                                        Upload file baru untuk mengganti file yang ada. Kosongkan jika tidak ingin mengubah file.
                                    </p>
                                </div>
                                @error('file') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-3">
                            <a href="{{ $this->getBackUrl() }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

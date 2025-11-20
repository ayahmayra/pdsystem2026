<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex items-center mb-6">
                <a href="{{ $this->getBackUrl() }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    ← Kembali ke Dokumen
                </a>
                <h2 class="text-2xl font-bold text-gray-900">Upload Dokumen Pendukung</h2>
            </div>

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

            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif

            <!-- Upload Form -->
            <div class="bg-white shadow sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Dokumen Baru</h3>
                    <form wire:submit="save">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Document Type -->
                            <div>
                                <label for="document_type" class="block text-sm font-medium text-gray-700">
                                    Jenis Dokumen <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="document_type" id="document_type" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Pilih Jenis Dokumen</option>
                                    <option value="UNDANGAN">Undangan</option>
                                    <option value="FOTO">Foto Kegiatan</option>
                                    <option value="SERTIFIKAT">Sertifikat</option>
                                    <option value="NOTULEN">Notulen</option>
                                    <option value="DOKUMEN_LAIN">Dokumen Lainnya</option>
                                </select>
                                @error('document_type') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">
                                    Judul Dokumen <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="title" type="text" id="title" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="Contoh: Undangan Rapat Koordinasi">
                                @error('title') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="sm:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Deskripsi
                                </label>
                                <textarea wire:model="description" id="description" rows="3" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                          placeholder="Deskripsi dokumen (opsional)"></textarea>
                            </div>

                            <!-- File Upload -->
                            <div class="sm:col-span-2">
                                <label for="file" class="block text-sm font-medium text-gray-700">
                                    File <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="file" type="file" id="file" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <p class="mt-1 text-sm text-gray-500">Maksimal 10MB. Format: PDF, JPG, PNG, DOC, DOCX</p>
                                @error('file') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Upload Dokumen
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Documents List -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dokumen Pendukung</h3>
                    
                    @if($documents->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jenis
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Judul
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            File
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ukuran
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Upload Oleh
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($documents as $index => $document)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $document->document_type }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div>
                                                    <div class="font-medium">{{ $document->title }}</div>
                                                    @if($document->description)
                                                        <div class="text-gray-500 text-xs">{{ $document->description }}</div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $document->file_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $document->file_size_human }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $document->uploadedByUser->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ $document->file_url }}" target="_blank" 
                                                       class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50" title="Download">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('supporting-documents.edit', ['notaDinas' => $notaDinas->id, 'document' => $document->id]) }}" 
                                                       class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-50" title="Edit">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                    <button wire:click="deleteDocument({{ $document->id }})" 
                                                            wire:confirm="Apakah Anda yakin ingin menghapus dokumen ini?"
                                                            class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50" title="Hapus">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada dokumen</h3>
                            <p class="mt-1 text-sm text-gray-500">Upload dokumen pendukung untuk laporan ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

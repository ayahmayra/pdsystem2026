<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Konfigurasi Organisasi</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola informasi dan pengaturan organisasi/OPD</p>
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

    <form wire:submit="save" class="space-y-8">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Dasar</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Informasi dasar tentang organisasi/OPD</p>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Organisasi -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Organisasi *
                        </label>
                        <input 
                            type="text" 
                            wire:model="name" 
                            placeholder="Contoh: Badan Pengelola Keuangan dan Aset Daerah"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        />
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nama Singkat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Singkat/Akronim
                        </label>
                        <input 
                            type="text" 
                            wire:model="short_name" 
                            placeholder="Contoh: BPKAD"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        />
                        @error('short_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Provinsi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Provinsi
                        </label>
                        <input 
                            type="text" 
                            wire:model="province" 
                            placeholder="Contoh: Riau"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        />
                        @error('province') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kota -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Kota/Kabupaten
                        </label>
                        <input 
                            type="text" 
                            wire:model="city" 
                            placeholder="Contoh: Bengkalis"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        />
                        @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Alamat Lengkap
                        </label>
                        <textarea 
                            wire:model="address" 
                            rows="3"
                            placeholder="Contoh: Jl. Jendral Sudirman No. 1, Bengkalis"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        ></textarea>
                        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Kontak</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Informasi kontak dan komunikasi</p>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Telepon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nomor Telepon
                        </label>
                        <input 
                            type="text" 
                            wire:model="phone" 
                            placeholder="Contoh: 0766-123456"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        />
                        @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email
                        </label>
                        <input 
                            type="email" 
                            wire:model="email" 
                            placeholder="Contoh: bpkad@example.go.id"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        />
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Website -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Website
                        </label>
                        <input 
                            type="url" 
                            wire:model="website" 
                            placeholder="Contoh: https://bpkad.example.go.id"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        />
                        @error('website') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Head Information -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Pimpinan</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Informasi kepala/pimpinan organisasi</p>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kepala -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Kepala/Pimpinan
                        </label>
                        <select 
                            wire:model="head_user_id"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="">Pilih Kepala/Pimpinan</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->fullNameWithTitles() }}
                                    @if($user->position)
                                        - {{ $user->position->name }}
                                        @if($user->position->echelon)
                                            ({{ $user->position->echelon->fullName() }})
                                        @endif
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('head_user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jabatan Kepala -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Sebutan Jabatan *
                        </label>
                        <input 
                            type="text" 
                            wire:model="head_title" 
                            placeholder="Contoh: Kepala Badan"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        />
                        @error('head_title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Sebutan jabatan yang akan muncul di dokumen (Kepala Dinas, Kepala Badan, dll)
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Digital Assets -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Aset Digital</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload logo, tanda tangan dan stempel digital</p>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Logo Apps -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Logo Aplikasi
                        </label>
                        
                        @if($current_logo_path)
                            <div class="mb-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-green-700 dark:text-green-300 text-sm">Logo sudah terupload</span>
                                    </div>
                                    <button 
                                        type="button"
                                        wire:click="removeLogo"
                                        wire:confirm="Hapus logo yang sudah ada?"
                                        class="text-red-500 hover:text-red-700 text-sm"
                                    >
                                        Hapus
                                    </button>
                                </div>
                                <img src="{{ Storage::url($current_logo_path) }}" alt="Logo" class="mt-2 max-h-20 border rounded">
                            </div>
                        @endif

                        <input 
                            type="file" 
                            wire:model="logo_file" 
                            accept="image/*"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        @error('logo_file') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Format: JPG, PNG (Max: 2MB)</p>
                    </div>

                    <!-- Tanda Tangan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tanda Tangan Digital
                        </label>
                        
                        @if($current_signature_path)
                            <div class="mb-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-green-700 dark:text-green-300 text-sm">Tanda tangan sudah terupload</span>
                                    </div>
                                    <button 
                                        type="button"
                                        wire:click="removeSignature"
                                        wire:confirm="Hapus tanda tangan yang sudah ada?"
                                        class="text-red-500 hover:text-red-700 text-sm"
                                    >
                                        Hapus
                                    </button>
                                </div>
                                <img src="{{ Storage::url($current_signature_path) }}" alt="Tanda Tangan" class="mt-2 max-h-20 border rounded">
                            </div>
                        @endif

                        <input 
                            type="file" 
                            wire:model="signature_file" 
                            accept="image/*"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        @error('signature_file') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Format: JPG, PNG (Max: 2MB)</p>
                    </div>

                    <!-- Stempel -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Stempel Digital
                        </label>
                        
                        @if($current_stamp_path)
                            <div class="mb-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-green-700 dark:text-green-300 text-sm">Stempel sudah terupload</span>
                                    </div>
                                    <button 
                                        type="button"
                                        wire:click="removeStamp"
                                        wire:confirm="Hapus stempel yang sudah ada?"
                                        class="text-red-500 hover:text-red-700 text-sm"
                                    >
                                        Hapus
                                    </button>
                                </div>
                                <img src="{{ Storage::url($current_stamp_path) }}" alt="Stempel" class="mt-2 max-h-20 border rounded">
                            </div>
                        @endif

                        <input 
                            type="file" 
                            wire:model="stamp_file" 
                            accept="image/*"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        @error('stamp_file') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Format: JPG, PNG (Max: 2MB)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Settings -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Pengaturan Dokumen</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Konfigurasi format dan tampilan dokumen</p>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Separator -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Pemisah Tahun/Bulan *
                        </label>
                        <input 
                            type="text" 
                            wire:model="ym_separator" 
                            placeholder="/"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        />
                        @error('ym_separator') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Karakter pemisah dalam nomor surat (contoh: "/" menghasilkan 001/2024)
                        </p>
                    </div>

                    <!-- Logo Settings -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Pengaturan Logo Kop Surat
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model="show_left_logo" 
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                />
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Tampilkan logo kiri</span>
                            </label>
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model="show_right_logo" 
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                />
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Tampilkan logo kanan</span>
                            </label>
                        </div>
                    </div>

                    <!-- QR Footer -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Teks Footer QR Code
                        </label>
                        <input 
                            type="text" 
                            wire:model="qr_footer_text" 
                            placeholder="Verifikasi keaslian dokumen via QR."
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        />
                        @error('qr_footer_text') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Teks yang muncul di bawah QR code pada dokumen
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button 
                type="submit"
                class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan Konfigurasi
            </button>
        </div>
    </form>
</div>

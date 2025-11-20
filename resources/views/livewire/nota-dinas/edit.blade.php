<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Edit Nota Dinas
                    </h2>
                    <a 
                        href="{{ route('documents') }}" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                    >
                        Kembali
                    </a>
                </div>

                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif


                <form wire:submit="update">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Dasar</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Unit Pemohon -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Unit Pemohon <span class="text-red-500">*</span>
                                </label>
                                @if($isRequestingUnitDisabled)
                                    <div class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400">
                                        {{ $units->first()->name ?? 'Unit tidak tersedia' }}
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Unit Pemohon otomatis sesuai dengan unit Anda
                                    </p>
                                @else
                                    <flux:select wire:model="requesting_unit_id" variant="listbox" searchable placeholder="Pilih Unit...">
                                        <flux:select.option value="">Pilih Unit</flux:select.option>
                                        @foreach($units as $unit)
                                            <flux:select.option value="{{ $unit->id }}">{{ $unit->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                @endif
                                @error('requesting_unit_id') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Kepada -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Kepada <span class="text-red-500">*</span>
                                </label>
                                <flux:select wire:model="to_user_id" variant="listbox" searchable placeholder="Pilih Pegawai...">
                                    <flux:select.option value="">Pilih Pegawai</flux:select.option>
                                    @foreach($users as $user)
                                        <flux:select.option value="{{ $user->id }}">{{ $user->fullNameWithTitles() }} - {{ $user->position?->name ?? 'N/A' }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error('to_user_id') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Dari -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Dari <span class="text-red-500">*</span>
                                </label>
                                <flux:select wire:model="from_user_id" variant="listbox" searchable placeholder="Pilih Pegawai...">
                                    <flux:select.option value="">Pilih Pegawai</flux:select.option>
                                    @foreach($users as $user)
                                        <flux:select.option value="{{ $user->id }}">{{ $user->fullNameWithTitles() }} - {{ $user->position?->name ?? 'N/A' }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error('from_user_id') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Kota Tujuan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Kota Tujuan <span class="text-red-500">*</span>
                                </label>
                                <flux:select wire:model="destination_city_id" variant="listbox" searchable placeholder="Pilih Kota...">
                                    <flux:select.option value="">Pilih Kota</flux:select.option>
                                    @foreach($cities as $city)
                                        <flux:select.option value="{{ $city->id }}">{{ $city->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error('destination_city_id') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Tempat Asal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tempat Asal <span class="text-red-500">*</span>
                                </label>
                                <flux:select wire:model="origin_place_id" variant="listbox" searchable placeholder="Pilih Tempat...">
                                    <flux:select.option value="">Pilih Tempat</flux:select.option>
                                    @foreach($orgPlaces as $place)
                                        <flux:select.option value="{{ $place->id }}">{{ $place->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error('origin_place_id') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Sifat -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Sifat <span class="text-red-500">*</span>
                                </label>
                                <flux:select wire:model="sifat" variant="listbox" searchable placeholder="Pilih Sifat...">
                                    <flux:select.option value="Penting">Penting</flux:select.option>
                                    <flux:select.option value="Segera">Segera</flux:select.option>
                                    <flux:select.option value="Biasa">Biasa</flux:select.option>
                                    <flux:select.option value="Rahasia">Rahasia</flux:select.option>
                                </flux:select>
                                @error('sifat') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Tanggal Nota Dinas -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tanggal Nota Dinas <span class="text-red-500">*</span>
                                </label>
                                <input type="date" wire:model="nd_date" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                @error('nd_date') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Hal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Hal <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="hal" placeholder="Masukkan hal surat" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                @error('hal') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Document Number Section -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Nomor Dokumen</h3>
                        <div class="space-y-4">
                            <!-- Manual Number Toggle -->
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="number_is_manual" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Gunakan nomor manual</label>
                            </div>

                            <!-- Manual Number Fields -->
                            @if($number_is_manual)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Nomor Dokumen <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="doc_no" placeholder="Masukkan nomor dokumen" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                        @error('doc_no') 
                                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Alasan Nomor Manual <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="number_manual_reason" placeholder="Masukkan alasan" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                        @error('number_manual_reason') 
                                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Trip Information -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Perjalanan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Tanggal Mulai -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" wire:model="start_date" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                @error('start_date') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Tanggal Selesai -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tanggal Selesai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" wire:model="end_date" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                @error('end_date') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Jenis Perjalanan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Jenis Perjalanan <span class="text-red-500">*</span>
                                </label>
                                <flux:select wire:model="trip_type" variant="listbox" searchable placeholder="Pilih Jenis Perjalanan...">
                                    <flux:select.option value="LUAR_DAERAH">Luar Daerah</flux:select.option>
                                    <flux:select.option value="DALAM_DAERAH_GT8H">Dalam Daerah > 8 Jam</flux:select.option>
                                    <flux:select.option value="DALAM_DAERAH_LE8H">Dalam Daerah ≤ 8 Jam</flux:select.option>
                                    <flux:select.option value="DIKLAT">Diklat</flux:select.option>
                                </flux:select>
                                @error('trip_type') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Isi Surat</h3>
                        <div class="space-y-4">
                            <!-- Dasar -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Dasar <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="dasar" rows="3" placeholder="Masukkan dasar surat" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                                @error('dasar') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Maksud -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Maksud <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="maksud" rows="3" placeholder="Masukkan maksud surat" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                                @error('maksud') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Participants Section -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Peserta Perjalanan Dinas</h3>
                        
                        <!-- Add Participant -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tambah Peserta</label>
                            <flux:select wire:model.live="selectedUser" variant="listbox" searchable placeholder="Pilih pegawai untuk ditambahkan...">
                                <flux:select.option value="">Pilih Pegawai</flux:select.option>
                                @foreach($users as $user)
                                    @if(!in_array($user->id, $participants ?? []))
                                        <flux:select.option value="{{ $user->id }}">{{ $user->fullNameWithTitles() }} - {{ $user->position?->name ?? 'N/A' }}</flux:select.option>
                                    @endif
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- Selected Participants -->
                        @if(!empty($participants) && count($participants) > 0)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Peserta Terpilih ({{ count($participants) }})</label>
                                <div class="space-y-2">
                                    @foreach($participants as $participantId)
                                        @php
                                            $user = collect($users)->firstWhere('id', $participantId);
                                        @endphp
                                        @if($user)
                                            <div class="flex items-center justify-between bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                                <div class="flex-1">
                                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $user->fullNameWithTitles() }}</div>
                                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $user->position?->name ?? 'N/A' }} - {{ $user->unit?->name ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                <button type="button" 
                                                        wire:click="removeParticipant({{ $participantId }})"
                                                        class="ml-3 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="mt-2">Belum ada peserta yang dipilih</p>
                            </div>
                        @endif
                        
                        @error('participants') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Overlap Warning -->
                    @if($showOverlapWarning && !empty($overlapDetails))
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Peringatan Konflik Jadwal
                            </h3>
                            <div class="text-sm text-yellow-700 dark:text-yellow-300">
                                <p class="mb-3">Beberapa peserta memiliki jadwal yang bentrok dengan perjalanan dinas ini:</p>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-yellow-200 dark:divide-yellow-700">
                                        <thead>
                                            <tr class="bg-yellow-100 dark:bg-yellow-800/50">
                                                <th class="px-3 py-2 text-left font-medium text-yellow-800 dark:text-yellow-200">Peserta</th>
                                                <th class="px-3 py-2 text-left font-medium text-yellow-800 dark:text-yellow-200">No. Dokumen</th>
                                                <th class="px-3 py-2 text-left font-medium text-yellow-800 dark:text-yellow-200">Unit</th>
                                                <th class="px-3 py-2 text-left font-medium text-yellow-800 dark:text-yellow-200">Hal</th>
                                                <th class="px-3 py-2 text-left font-medium text-yellow-800 dark:text-yellow-200">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($overlapDetails as $overlap)
                                                <tr class="border-b border-yellow-200 dark:border-yellow-700">
                                                    <td class="px-3 py-2 text-yellow-700 dark:text-yellow-300">{{ $overlap['user'] }}</td>
                                                    <td class="px-3 py-2 text-yellow-700 dark:text-yellow-300">{{ $overlap['doc_no'] }}</td>
                                                    <td class="px-3 py-2 text-yellow-700 dark:text-yellow-300">{{ $overlap['unit'] }}</td>
                                                    <td class="px-3 py-2 text-yellow-700 dark:text-yellow-300">{{ $overlap['hal'] }}</td>
                                                    <td class="px-3 py-2 text-yellow-700 dark:text-yellow-300">
                                                        {{ \Carbon\Carbon::parse($overlap['start_date'])->format('d/m/Y') }} - 
                                                        {{ \Carbon\Carbon::parse($overlap['end_date'])->format('d/m/Y') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Additional Information -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Tambahan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Jumlah Lampiran -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Jumlah Lampiran <span class="text-red-500">*</span>
                                </label>
                                <input type="number" wire:model="lampiran_count" min="1" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                @error('lampiran_count') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <flux:select wire:model="status" variant="listbox" searchable placeholder="Pilih Status...">
                                    <flux:select.option value="DRAFT">Draft</flux:select.option>
                                    <flux:select.option value="APPROVED">Approved</flux:select.option>
                                </flux:select>
                                @error('status') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Tembusan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tembusan</label>
                                <textarea wire:model="tembusan" rows="2" placeholder="Masukkan tembusan (opsional)" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                                @error('tembusan') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                                <textarea wire:model="notes" rows="2" placeholder="Masukkan catatan (opsional)" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                                @error('notes') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <a href="{{ route('documents') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Batal
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Nota Dinas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
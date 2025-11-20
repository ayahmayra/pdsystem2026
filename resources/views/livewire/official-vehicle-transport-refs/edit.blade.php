<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Referensi Transportasi Kendaraan Dinas</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Edit referensi tarif transportasi kendaraan dinas/operasional</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('reference-rates.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-flux::icon.chevrons-up-down class="w-4 h-4 mr-2" />
                Kembali ke Referensi Tarif
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <form wire:submit="save" class="space-y-6 p-6">
            <!-- Origin Place -->
            <div>
                <label for="origin_place_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tempat Kerja <span class="text-red-500">*</span>
                </label>
                <select
                    wire:model="origin_place_id"
                    id="origin_place_id"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                >
                    <option value="">Pilih Tempat Kerja</option>
                    @foreach($orgPlaces as $orgPlace)
                        <option value="{{ $orgPlace->id }}">
                            {{ $orgPlace->name }} - {{ $orgPlace->city->name }}, {{ $orgPlace->city->province->name }}
                        </option>
                    @endforeach
                </select>
                @error('origin_place_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Destination District -->
            <div>
                <label for="destination_district_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Kecamatan Tujuan <span class="text-red-500">*</span>
                </label>
                <select
                    wire:model="destination_district_id"
                    id="destination_district_id"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                >
                    <option value="">Pilih Kecamatan Tujuan</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}">
                            {{ $district->name }} - {{ $district->city->name }}, {{ $district->city->province->name }}
                        </option>
                    @endforeach
                </select>
                @error('destination_district_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Context -->
            <div>
                <label for="context" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Kedudukan <span class="text-red-500">*</span>
                </label>
                <select
                    wire:model="context"
                    id="context"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                >
                    <option value="">Pilih Kedudukan</option>
                    <option value="Kedudukan Bengkalis">Kedudukan Bengkalis</option>
                    <option value="Kedudukan Duri">Kedudukan Duri</option>
                </select>
                @error('context')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- PP Amount -->
            <div>
                <label for="pp_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tarif Per Orang (Rp) <span class="text-red-500">*</span>
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <input
                        type="number"
                        wire:model="pp_amount"
                        id="pp_amount"
                        step="0.01"
                        min="0"
                        class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="0.00"
                    />
                </div>
                @error('pp_amount')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('reference-rates.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Batal
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

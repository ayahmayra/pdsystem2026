<div class="p-4 sm:p-6 lg:p-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Rekap Global Perjalanan Dinas</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                Rekapitulasi menyeluruh semua dokumen perjalanan dinas (Nota Dinas, SPT, SPPD, Kwitansi, Laporan Perjalanan Dinas, Dokumen Pendukung).
            </p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <button wire:click="exportPdf" class="inline-flex items-center justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export PDF
            </button>
            <button wire:click="exportExcel" class="ml-3 inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:w-auto">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    <div class="mt-8 flow-root">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <div class="flex-grow max-w-xs">
                <label for="search" class="sr-only">Search</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input id="search" wire:model.live.debounce.300ms="search" type="search" name="search" class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600" placeholder="Cari dokumen, pegawai, tujuan..." />
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label for="dateFrom" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Dari Tanggal</label>
                    <input type="date" wire:model.live="dateFrom" id="dateFrom" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                </div>
                <div>
                    <label for="dateTo" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Sampai Tanggal</label>
                    <input type="date" wire:model.live="dateTo" id="dateTo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                </div>
                <div>
                    <label for="locationFilter" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Lokasi</label>
                    <select wire:model.live="locationFilter" id="locationFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                        <option value="">Semua Lokasi</option>
                        @foreach($locationOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <button wire:click="clearFilters" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-gray-600 dark:text-white dark:border-gray-500 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- Modern Table Container -->
        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg overflow-hidden">
            <style>
                .group-border-top {
                    box-shadow: inset 0 4px 0 #000000 !important;
                }
                .group-border-bottom {
                    box-shadow: inset 0 -4px 0 #000000 !important;
                }
                .group-border-both {
                    box-shadow: inset 0 4px 0 #000000, inset 0 -4px 0 #000000 !important;
                }
                /* Cell borders for all table cells */
                .table-cell-border {
                    border-width: 1px; border-color: rgba(181, 181, 181, 0.25); !important;
                }
                /* Override existing border-r classes */
                .border-r {
                    border-width: 1px; border-color: rgba(181, 181, 181, 0.25); !important;
                }
                /* Thick vertical borders for column groups */
                .group-border-thick-right {
                    border-right: 3px solid #000000 !important;
                }
                .group-border-thick-left {
                    border-left: 3px solid #000000 !important;
                }
                /* Header group borders */
                .header-group-border {
                    border-right: 3px solid #000000 !important;
                }
            </style>
            <div class="overflow-auto max-h-[calc(100vh-200px)] min-h-[400px] scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 dark:scrollbar-thumb-gray-600 dark:scrollbar-track-gray-800" style="scrollbar-width: thin; scrollbar-color: #d1d5db #f3f4f6; overflow-x: auto; overflow-y: auto; position: relative;">
                 <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" style="min-width: 2850px; table-layout: auto; border-collapse: separate; border-spacing: 0;">
                        <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-30" style="position: sticky; top: 0; z-index: 30; background-color: #f9fafb;">
                            <tr>
                                 <th scope="col" class="px-2 py-1 text-center text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-600 table-cell-border header-group-border" style="width: 380px;" colspan="2">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-gray-900 dark:text-white">Nota Dinas</span>
                                    </div>
                                </th>
                                <th scope="col" class="px-2 py-1 text-center text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-600 table-cell-border header-group-border" style="width: 350px;" colspan="2">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <span class="text-gray-900 dark:text-white font-semibold">SPT</span>
                                    </div>
                                </th>
                                <th scope="col" class="px-2 py-1 text-center text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-600 table-cell-border header-group-border" style="width: 670px;" colspan="4">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span class="text-gray-900 dark:text-white font-semibold">SPPD</span>
                                    </div>
                                </th>
                                <th scope="col" class="px-2 py-1 text-center text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-600 table-cell-border header-group-border" style="width: 150px;">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-gray-900 dark:text-white font-semibold">Laporan</span>
                                    </div>
                                </th>
                                <th scope="col" class="px-2 py-1 text-center text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-600 table-cell-border header-group-border" style="width: 400px;" colspan="2">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        <span class="text-gray-900 dark:text-white font-semibold">Kwitansi</span>
                                    </div>
                                </th>
                                <th scope="col" class="px-2 py-1 text-center text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-600 table-cell-border header-group-border" style="width: 300px;" colspan="3">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                        <span class="text-gray-900 dark:text-white font-semibold">Transportasi</span>
                                    </div>
                                </th>
                                <th scope="col" class="px-2 py-1 text-center text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-600 table-cell-border header-group-border" style="width: 300px;" colspan="3">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        </svg>
                                        <span class="text-gray-900 dark:text-white font-semibold">Penginapan</span>
                                    </div>
                                </th>
                                <th scope="col" class="px-2 py-1 text-center text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-600 table-cell-border header-group-border" style="width: 225px;" colspan="3">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-gray-900 dark:text-white font-semibold">Uang Harian</span>
                                    </div>
                                </th>
                                <th scope="col" class="px-2 py-1 text-center text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-600 table-cell-border header-group-border" style="width: 225px;" colspan="3">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span class="text-gray-900 dark:text-white font-semibold">Representatif</span>
                                    </div>
                                </th>
                                <th scope="col" class="px-2 py-1 text-center text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-600 table-cell-border header-group-border" style="width: 300px;" colspan="3">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        <span class="text-gray-900 dark:text-white font-semibold">Biaya Lainnya</span>
                                    </div>
                                </th>
                                <th scope="col" class="px-2 py-1 text-center text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-600 table-cell-border header-group-border" style="width: 150px;">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-gray-900 dark:text-white font-semibold">Total Kwitansi</span>
                                    </div>
                                </th>
                                <th scope="col" class="px-2 py-1 text-center text-sm font-medium text-gray-900 dark:text-white" style="width: 200px;">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        <span class="text-gray-900 dark:text-white font-semibold">Dokumen Pendukung</span>
                                    </div>
                                </th>
                            </tr>
                            <tr class="bg-gray-100 dark:bg-gray-700 sticky  z-30" style="position: sticky;  z-index: 30; background-color: #f3f4f6;">
                                <!-- Nota Dinas sub-columns -->
                                <th scope="col" class="py-3 pl-4 pr-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 180px;">No. Nota Dinas</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 200px;">Asal & Tujuan</th>
                                <!-- SPT sub-columns -->
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 150px;">No. & Tanggal SPT</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 200px;">Penandatangan SPT</th>
                                <!-- SPPD sub-columns -->
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 150px;">No. & Tanggal SPPD</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 200px;">Penandatangan SPPD</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 120px;">Alat Angkutan</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 200px;">Nama PPTK</th>
                                <!-- Laporan sub-column -->
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 150px;">No. & Tanggal Laporan</th>
                                <!-- Kwitansi sub-columns -->
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 300px;">Nama Peserta</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 100px;">No. & Tanggal Kwitansi</th>
                                <!-- Transportasi sub-columns -->
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 100px;">Uraian</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 100px;">Nilai</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 100px;">Deskripsi</th>
                                <!-- Penginapan sub-columns -->
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 100px;">Uraian</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 100px;">Nilai</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 100px;">Deskripsi</th>
                                <!-- Uang Harian sub-columns -->
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 75px;">Uraian</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 75px;">Nilai</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 75px;">Deskripsi</th>
                                <!-- Representatif sub-columns -->
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 75px;">Uraian</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 75px;">Nilai</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 75px;">Deskripsi</th>
                                <!-- Biaya Lainnya sub-columns -->
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 100px;">Uraian</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border " style="width: 100px;">Nilai</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 100px;">Deskripsi</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 150px;">Total Kwitansi</th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300" style="width: 200px;">Dokumen Pendukung</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                            <!-- Spacer row for sticky header -->
                            <tr style="height: 0px; background: transparent;">
                                 <td colspan="28" style="padding: 0; border: none; background: transparent;"></td>
                            </tr>
                            @forelse($rekapData as $index => $item)
                                @php
                                    // Determine if this is a new group (new nota dinas)
                                    $isNewGroup = $index === 0 || 
                                        ($item['id'] && isset($rekapData[$index-1]) && $rekapData[$index-1]['id'] !== $item['id']);
                                    
                                    // Determine if this is the last row in the group
                                    $isLastInGroup = $index === count($rekapData) - 1 || 
                                        ($item['id'] && isset($rekapData[$index+1]) && 
                                         $rekapData[$index+1]['id'] !== $item['id'] && 
                                         $rekapData[$index+1]['id'] !== null);
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 ease-in-out @if($isNewGroup && $isLastInGroup) group-border-both @elseif($isNewGroup) group-border-top @elseif($isLastInGroup) group-border-bottom @endif">
                                    <!-- No. & Tanggal -->
                                    <td class="px-2 py-1 text-xs whitespace-nowrap border-r border-gray-200 dark:border-gray-600" 
                                        style="width: 180px;">
                                        @if($item['id'])
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('nota-dinas.show', $item['id']) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">
                                                    {{ $item['number'] ?: 'N/A' }}
                                                </a>
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-400">
                                                {{ $item['date'] ? \Carbon\Carbon::parse($item['date'])->format('d/m/Y') : 'N/A' }}
                                            </div>
                                            @if($item['requesting_unit'])
                                                <div class="text-xs text-gray-400 mt-1">
                                                    Bidang: {{ $item['requesting_unit'] }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Asal & Tujuan -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border group-border-thick-right whitespace-nowrap @if($isNewGroup) group-border-top-all @endif @if($isLastInGroup) group-border-bottom-all @endif" style="width: 200px;">
                                        @if($item['origin'])
                                            <div class="text-gray-900 dark:text-white">
                                                <div class="font-medium">{{ $item['origin'] }}</div>
                                                <div class="text-gray-500 dark:text-gray-400">→ {{ $item['destination'] }}</div>
                                            </div>
                                            @if($item['start_date'] && $item['end_date'])
                                                <div class="mt-1 text-xs text-gray-400">
                                                    {{ \Carbon\Carbon::parse($item['start_date'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($item['end_date'])->format('d/m/Y') }}
                                                    <span class="ml-1">({{ $item['duration'] ?: \Carbon\Carbon::parse($item['start_date'])->diffInDays(\Carbon\Carbon::parse($item['end_date'])) + 1 }} Hari)</span>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    

                                    <!-- No. & Tanggal SPT -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border whitespace-nowrap @if($isNewGroup) group-border-top-all @endif @if($isLastInGroup) group-border-bottom-all @endif" style="width: 150px;">
                                        @if($item['spt_number'] && $item['spt_id'])
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('spt.pdf', $item['spt_id']) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">
                                                    {{ $item['spt_number'] }}
                                                </a>
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-400">
                                                {{ $item['spt_date'] ? \Carbon\Carbon::parse($item['spt_date'])->format('d/m/Y') : 'N/A' }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Penandatangan SPT -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border group-border-thick-right whitespace-nowrap" style="width: 200px;">
                                        @if($item['spt_signer'])
                                            <div class="text-gray-900 dark:text-white">
                                                {{ $item['spt_signer'] }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- No. & Tanggal SPPD -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border whitespace-nowrap" style="width: 150px;">
                                        @if($item['sppd_number'] && $item['sppd_id'])
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('sppd.pdf', $item['sppd_id']) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">
                                                    {{ $item['sppd_number'] }}
                                                </a>
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-400">
                                                {{ $item['sppd_date'] ? \Carbon\Carbon::parse($item['sppd_date'])->format('d/m/Y') : 'N/A' }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Penandatangan SPPD -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border whitespace-nowrap" style="width: 200px;">
                                        @if($item['sppd_signer'])
                                            <div class="text-gray-900 dark:text-white">
                                                {{ $item['sppd_signer'] }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Alat Angkutan -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border whitespace-nowrap" style="width: 120px;">
                                        @if($item['transport_mode'])
                                            <div class="text-gray-900 dark:text-white">
                                                {{ $item['transport_mode'] }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Nama PPTK -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border group-border-thick-right whitespace-nowrap" style="width: 200px;">
                                        @if($item['pptk_name'])
                                            <div class="text-gray-900 dark:text-white">
                                                {{ $item['pptk_name'] }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- No. & Tanggal Laporan -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border group-border-thick-right whitespace-nowrap" style="width: 150px;">
                                        @if($item['trip_report_number'] && $item['trip_report_id'])
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('trip-reports.pdf', $item['trip_report_id']) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">
                                                    {{ $item['trip_report_number'] }}
                                                </a>
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-400">
                                                {{ $item['trip_report_date'] ? \Carbon\Carbon::parse($item['trip_report_date'])->format('d/m/Y') : 'N/A' }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Nama Peserta -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border whitespace-nowrap" style="width: 300px;">
                                        @if($item['participant_name'])
                                            <div class="text-gray-900 dark:text-white">
                                                <div class="font-medium">{{ $item['participant_name'] }}</div>
                                                @if($item['participant_nip'])
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        NIP: {{ $item['participant_nip'] }}
                                                    </div>
                                                @endif
                                                @if($item['participant_rank'])
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $item['participant_rank'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- No. & Tanggal Kwitansi -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border group-border-thick-right whitespace-nowrap" style="width: 100px;">
                                        @if($item['receipt_number'] && $item['receipt_id'])
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('receipts.pdf', $item['receipt_id']) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">
                                                    {{ $item['receipt_number'] }}
                                                </a>
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-400">
                                                {{ $item['receipt_date'] ? \Carbon\Carbon::parse($item['receipt_date'])->format('d/m/Y') : 'N/A' }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Transportasi - Uraian -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border whitespace-nowrap" style="width: 100px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'transport' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="text-gray-700 dark:text-gray-300">
                                                <div class="font-medium">
                                                    ({{ number_format($item['receipt_line']['line']['qty'], 0, ',', '.') }} x Rp {{ number_format($item['receipt_line']['line']['unit_amount'], 0, ',', '.') }})
                                                </div>
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['transport']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['transport'] as $line)
                                                    <div class="text-gray-700 dark:text-gray-300">
                                                        <div class="font-medium">
                                                            ({{ number_format($line['qty'], 0, ',', '.') }} x Rp {{ number_format($line['unit_amount'], 0, ',', '.') }})
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Transportasi - Nilai -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border text-right whitespace-nowrap" style="width: 100px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'transport' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="font-semibold text-gray-900 dark:text-white">
                                                Rp {{ number_format($item['receipt_line']['line']['line_total'], 0, ',', '.') }}
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['transport']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['transport'] as $line)
                                                    <div class="font-semibold text-gray-900 dark:text-white">
                                                        Rp {{ number_format($line['line_total'], 0, ',', '.') }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Transportasi - Deskripsi -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 100px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'transport' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="text-gray-600 dark:text-gray-400 text-xs">
                                                {{ $item['receipt_line']['line']['desc'] ?: '-' }}
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['transport']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['transport'] as $line)
                                                    <div class="text-gray-600 dark:text-gray-400 text-xs">
                                                        {{ $line['desc'] ?: '-' }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Penginapan - Uraian -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border whitespace-nowrap" style="width: 100px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'lodging' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="text-gray-700 dark:text-gray-300">
                                                <div class="font-medium">
                                                    @if($item['receipt_line']['line']['no_lodging'] && isset($item['receipt_line']['line']['reference_rate']))
                                                        ({{ number_format($item['receipt_line']['line']['qty'], 0, ',', '.') }} x (30% x Rp {{ number_format($item['receipt_line']['line']['reference_rate'], 0, ',', '.') }}))
                                                    @else
                                                        ({{ number_format($item['receipt_line']['line']['qty'], 0, ',', '.') }} x Rp {{ number_format($item['receipt_line']['line']['unit_amount'], 0, ',', '.') }})
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['lodging']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['lodging'] as $line)
                                                    <div class="text-gray-700 dark:text-gray-300">
                                                        <div class="font-medium">
                                                            @if($line['no_lodging'] && isset($line['reference_rate']))
                                                                ({{ number_format($line['qty'], 0, ',', '.') }} x (30% x Rp {{ number_format($line['reference_rate'], 0, ',', '.') }}))
                                                            @else
                                                            ({{ number_format($line['qty'], 0, ',', '.') }} x Rp {{ number_format($line['unit_amount'], 0, ',', '.') }})
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Penginapan - Nilai -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border text-right whitespace-nowrap" style="width: 100px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'lodging' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="font-semibold text-gray-900 dark:text-white">
                                                Rp {{ number_format($item['receipt_line']['line']['line_total'], 0, ',', '.') }}
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['lodging']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['lodging'] as $line)
                                                    <div class="font-semibold text-gray-900 dark:text-white">
                                                        Rp {{ number_format($line['line_total'], 0, ',', '.') }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Penginapan - Deskripsi -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 100px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'lodging' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="text-gray-600 dark:text-gray-400 text-xs">
                                                {{ $item['receipt_line']['line']['desc'] ?: '-' }}
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['lodging']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['lodging'] as $line)
                                                    <div class="text-gray-600 dark:text-gray-400 text-xs">
                                                        {{ $line['desc'] ?: '-' }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Uang Harian - Uraian -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border whitespace-nowrap" style="width: 75px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'perdiem' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="text-gray-700 dark:text-gray-300">
                                                <div class="font-medium">
                                                    ({{ number_format($item['receipt_line']['line']['qty'], 0, ',', '.') }} x Rp {{ number_format($item['receipt_line']['line']['unit_amount'], 0, ',', '.') }})
                                                </div>
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['perdiem']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['perdiem'] as $line)
                                                    <div class="text-gray-700 dark:text-gray-300">
                                                        <div class="font-medium">
                                                            ({{ number_format($line['qty'], 0, ',', '.') }} x Rp {{ number_format($line['unit_amount'], 0, ',', '.') }})
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Uang Harian - Nilai -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border text-right whitespace-nowrap" style="width: 75px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'perdiem' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="font-semibold text-gray-900 dark:text-white">
                                                Rp {{ number_format($item['receipt_line']['line']['line_total'], 0, ',', '.') }}
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['perdiem']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['perdiem'] as $line)
                                                    <div class="font-semibold text-gray-900 dark:text-white">
                                                        Rp {{ number_format($line['line_total'], 0, ',', '.') }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Uang Harian - Deskripsi -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 75px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'perdiem' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="text-gray-600 dark:text-gray-400 text-xs">
                                                {{ $item['receipt_line']['line']['desc'] ?: '-' }}
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['perdiem']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['perdiem'] as $line)
                                                    <div class="text-gray-600 dark:text-gray-400 text-xs">
                                                        {{ $line['desc'] ?: '-' }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Representatif - Uraian -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border whitespace-nowrap" style="width: 75px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'representation' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="text-gray-700 dark:text-gray-300">
                                                <div class="font-medium">
                                                    ({{ number_format($item['receipt_line']['line']['qty'], 0, ',', '.') }} x Rp {{ number_format($item['receipt_line']['line']['unit_amount'], 0, ',', '.') }})
                                                </div>
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['representation']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['representation'] as $line)
                                                    <div class="text-gray-700 dark:text-gray-300">
                                                        <div class="font-medium">
                                                            ({{ number_format($line['qty'], 0, ',', '.') }} x Rp {{ number_format($line['unit_amount'], 0, ',', '.') }})
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Representatif - Nilai -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border text-right whitespace-nowrap" style="width: 75px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'representation' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="font-semibold text-gray-900 dark:text-white">
                                                Rp {{ number_format($item['receipt_line']['line']['line_total'], 0, ',', '.') }}
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['representation']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['representation'] as $line)
                                                    <div class="font-semibold text-gray-900 dark:text-white">
                                                        Rp {{ number_format($line['line_total'], 0, ',', '.') }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Representatif - Deskripsi -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 75px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'representation' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="text-gray-600 dark:text-gray-400 text-xs">
                                                {{ $item['receipt_line']['line']['desc'] ?: '-' }}
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['representation']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['representation'] as $line)
                                                    <div class="text-gray-600 dark:text-gray-400 text-xs">
                                                        {{ $line['desc'] ?: '-' }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Biaya Lainnya - Uraian -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border whitespace-nowrap" style="width: 100px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'other' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="text-gray-700 dark:text-gray-300">
                                                <div class="font-medium">
                                                    ({{ number_format($item['receipt_line']['line']['qty'], 0, ',', '.') }} x Rp {{ number_format($item['receipt_line']['line']['unit_amount'], 0, ',', '.') }})
                                                </div>
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['other']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['other'] as $line)
                                                    <div class="text-gray-700 dark:text-gray-300">
                                                        <div class="font-medium">
                                                            ({{ number_format($line['qty'], 0, ',', '.') }} x Rp {{ number_format($line['unit_amount'], 0, ',', '.') }})
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Biaya Lainnya - Nilai -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border text-right whitespace-nowrap" style="width: 100px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'other' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="font-semibold text-gray-900 dark:text-white">
                                                Rp {{ number_format($item['receipt_line']['line']['line_total'], 0, ',', '.') }}
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['other']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['other'] as $line)
                                                    <div class="font-semibold text-gray-900 dark:text-white">
                                                        Rp {{ number_format($line['line_total'], 0, ',', '.') }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Biaya Lainnya - Deskripsi -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border group-border-thick-right" style="width: 100px;">
                                        @if(isset($item['receipt_line']) && $item['receipt_line'] && $item['receipt_line']['category'] === 'other' && $item['receipt_line']['line'])
                                            {{-- Additional row for specific category --}}
                                            <div class="text-gray-600 dark:text-gray-400 text-xs">
                                                {{ $item['receipt_line']['line']['desc'] ?: '-' }}
                                            </div>
                                        @elseif(isset($item['receipt_lines']) && !empty($item['receipt_lines']['other']))
                                            {{-- Main row with all categories --}}
                                            <div class="space-y-1">
                                                @foreach($item['receipt_lines']['other'] as $line)
                                                    <div class="text-gray-600 dark:text-gray-400 text-xs">
                                                        {{ $line['desc'] ?: '-' }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Total Kwitansi -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border group-border-thick-right text-right whitespace-nowrap" style="width: 150px;">
                                        @if($item['receipt_total'] !== null)
                                            <div class="font-semibold text-gray-900 dark:text-white">
                                                Rp {{ number_format($item['receipt_total'], 0, ',', '.') }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Dokumen Pendukung -->
                                    <td class="px-2 py-1 text-xs border-r border-gray-200 dark:border-gray-600 table-cell-border" 
                                        style="width: 200px;">
                                        @if(isset($item['supporting_documents']) && $item['supporting_documents'] && count($item['supporting_documents']) > 0)
                                            <div class="space-y-2">
                                                @foreach($item['supporting_documents'] as $doc)
                                                    <div class="flex items-start space-x-2">
                                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        <div class="flex-1 min-w-0">
                                                            <a href="{{ Storage::url($doc['file_path']) }}" 
                                                               target="_blank" 
                                                               download="{{ $doc['file_name'] }}"
                                                               class="block text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 text-xs font-medium hover:underline truncate" 
                                                               title="Download: {{ $doc['name'] }} ({{ $doc['file_name'] }})">
                                                                {{ $doc['name'] }}
                                                            </a>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                                {{ $doc['document_type'] }}
                                                                @if(isset($doc['file_size']) && $doc['file_size'])
                                                                    • {{ number_format($doc['file_size'] / 1024, 1) }} KB
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                     <td colspan="28" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 text-center dark:text-white">
                                        @if($loading)
                                            <div class="flex items-center justify-center">
                                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
                                                <span class="ml-2">Memuat data...</span>
                                            </div>
                                        @else
                                            Tidak ada data nota dinas yang ditemukan.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($totalRecords > $perPage)
            <div class="mt-6">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Menampilkan {{ (($this->getPage() - 1) * $perPage) + 1 }} sampai {{ min($this->getPage() * $perPage, $totalRecords) }} dari {{ $totalRecords }} data
                    </div>
                    <div class="flex space-x-2">
                        @if($this->getPage() > 1)
                            <button wire:click="previousPage" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded">Previous</button>
                        @endif
                        @if($this->getPage() < $this->getTotalPages())
                            <button wire:click="nextPage" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded">Next</button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
<x-layouts.app.sidebar title="Ref Lokasi & Rute">
    <flux:main>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Ref Lokasi & Rute</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Kelola semua data lokasi dan rute untuk perjalanan dinas
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                        @if(\App\Helpers\PermissionHelper::can('menu.provinces'))
                        <!-- Data Provinsi -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('provinces.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            Data Provinsi
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Kelola data provinsi di Indonesia
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(\App\Helpers\PermissionHelper::can('menu.cities'))
                        <!-- Data Kota/Kabupaten -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('cities.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            Data Kota/Kabupaten
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Kelola data kota dan kabupaten
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(\App\Helpers\PermissionHelper::can('menu.districts'))
                        <!-- Data Kecamatan -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('districts.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            Data Kecamatan
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Kelola data kecamatan
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(\App\Helpers\PermissionHelper::can('menu.org-places'))
                        <!-- Data Kedudukan -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('org-places.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            Data Kedudukan
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Kelola data kedudukan organisasi
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(\App\Helpers\PermissionHelper::can('menu.transport-modes'))
                        <!-- Data Moda Transportasi -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('transport-modes.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            Data Moda Transportasi
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Kelola data moda transportasi
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(\App\Helpers\PermissionHelper::can('menu.travel-routes'))
                        <!-- Data Rute Perjalanan -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('travel-routes.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            Data Rute Perjalanan
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Kelola data rute perjalanan
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>

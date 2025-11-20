<x-layouts.app.sidebar title="Master Data">
    <flux:main>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Master Data</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Kelola semua data master untuk perjalanan dinas
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                        <!-- Data Pegawai -->
                        @if(\App\Helpers\PermissionHelper::can('users.view'))
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('users.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            Data Pegawai
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Kelola data pegawai dan pengguna sistem
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Data Unit - Only for Admin and Super Admin -->
                        @if(\App\Helpers\PermissionHelper::can('master-data.view') && !auth()->user()->hasRole(['bendahara-pengeluaran', 'bendahara-pengeluaran-pembantu']))
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
                                        <a href="{{ route('units.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            Data Unit
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Kelola data unit organisasi dan struktur
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Data Sub Kegiatan -->
                        @if(\App\Helpers\PermissionHelper::can('master-data.view'))
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('sub-keg.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            Data Sub Kegiatan
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Kelola data sub kegiatan dan pagu anggaran
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Data Jabatan - Only for Admin and Super Admin -->
                        @if(\App\Helpers\PermissionHelper::can('master-data.view') && !auth()->user()->hasRole(['bendahara-pengeluaran', 'bendahara-pengeluaran-pembantu']))
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('positions.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            Data Jabatan
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Kelola data jabatan dan posisi pegawai
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Manajemen Role - Only for Super Admin -->
                        @if(\App\Helpers\PermissionHelper::canManagePermissions())
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('roles.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            Manajemen Role
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Kelola permissions untuk setiap role
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

{{-- Example: View with Permission-based Conditional Access --}}

@extends('components.layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                {{-- User Role Information --}}
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <h3 class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-2">
                        Informasi Akses Pengguna
                    </h3>
                    <div class="text-sm text-blue-700 dark:text-blue-300">
                        <p><strong>Role:</strong> {{ \App\Helpers\PermissionHelper::getUserRoleDisplayName() }}</p>
                        <p><strong>Unit:</strong> {{ auth()->user()->unit->name ?? 'Tidak ada unit' }}</p>
                        <p><strong>Dapat Akses Semua Data:</strong> {{ \App\Helpers\PermissionHelper::canAccessAllData() ? 'Ya' : 'Tidak' }}</p>
                    </div>
                </div>

                {{-- Navigation Menu based on Permissions --}}
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Menu Navigasi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        
                        {{-- Master Data Menu (Admin only) --}}
                        @if(\App\Helpers\PermissionHelper::can('master-data.view'))
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Master Data</h4>
                            <div class="space-y-2">
                                @if(\App\Helpers\PermissionHelper::can('master-data.view'))
                                    <a href="{{ route('master-data.index') }}" class="block text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                        📊 Lihat Master Data
                                    </a>
                                @endif
                                @if(\App\Helpers\PermissionHelper::can('master-data.create'))
                                    <a href="{{ route('master-data.create') }}" class="block text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                        ➕ Tambah Master Data
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Document Management Menu --}}
                        @if(\App\Helpers\PermissionHelper::canManageDocuments())
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Manajemen Dokumen</h4>
                            <div class="space-y-2">
                                @if(\App\Helpers\PermissionHelper::can('nota-dinas.view'))
                                    <a href="{{ route('nota-dinas.index') }}" class="block text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                        📄 Nota Dinas
                                    </a>
                                @endif
                                @if(\App\Helpers\PermissionHelper::can('spt.view'))
                                    <a href="{{ route('spt.index') }}" class="block text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                        📋 SPT
                                    </a>
                                @endif
                                @if(\App\Helpers\PermissionHelper::can('sppd.view'))
                                    <a href="{{ route('sppd.index') }}" class="block text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                        🎫 SPPD
                                    </a>
                                @endif
                                @if(\App\Helpers\PermissionHelper::can('receipts.view'))
                                    <a href="{{ route('receipts.index') }}" class="block text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                        💰 Kwitansi
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Rekapitulasi Menu --}}
                        @if(\App\Helpers\PermissionHelper::canViewRekap())
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Rekapitulasi</h4>
                            <div class="space-y-2">
                                @if(\App\Helpers\PermissionHelper::can('rekap.view'))
                                    <a href="{{ route('rekap.index') }}" class="block text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                        📊 Lihat Rekapitulasi
                                    </a>
                                @endif
                                @if(\App\Helpers\PermissionHelper::can('rekap.export'))
                                    <a href="{{ route('rekap.export') }}" class="block text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                        📤 Export Rekapitulasi
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- User Management Menu (Admin only) --}}
                        @if(\App\Helpers\PermissionHelper::can('users.view'))
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Manajemen User</h4>
                            <div class="space-y-2">
                                @if(\App\Helpers\PermissionHelper::can('users.view'))
                                    <a href="{{ route('users.index') }}" class="block text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                        👥 Lihat User
                                    </a>
                                @endif
                                @if(\App\Helpers\PermissionHelper::can('users.create'))
                                    <a href="{{ route('users.create') }}" class="block text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                        ➕ Tambah User
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

                {{-- Action Buttons based on Permissions --}}
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Aksi Cepat</h3>
                    <div class="flex flex-wrap gap-3">
                        
                        @if(\App\Helpers\PermissionHelper::can('nota-dinas.create'))
                        <a href="{{ route('nota-dinas.create') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            ➕ Buat Nota Dinas
                        </a>
                        @endif

                        @if(\App\Helpers\PermissionHelper::can('spt.create'))
                        <a href="{{ route('spt.create') }}" 
                           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            📋 Buat SPT
                        </a>
                        @endif

                        @if(\App\Helpers\PermissionHelper::can('sppd.create'))
                        <a href="{{ route('sppd.create') }}" 
                           class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            🎫 Buat SPPD
                        </a>
                        @endif

                        @if(\App\Helpers\PermissionHelper::can('receipts.create'))
                        <a href="{{ route('receipts.create') }}" 
                           class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                            💰 Buat Kwitansi
                        </a>
                        @endif

                        @if(\App\Helpers\PermissionHelper::canViewRekap())
                        <a href="{{ route('rekap.index') }}" 
                           class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            📊 Lihat Rekapitulasi
                        </a>
                        @endif

                    </div>
                </div>

                {{-- Role-specific Information --}}
                @if(\App\Helpers\PermissionHelper::hasRole('super-admin'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <h4 class="text-lg font-medium text-red-800 dark:text-red-200 mb-2">
                        ⚠️ Super Admin Access
                    </h4>
                    <p class="text-sm text-red-700 dark:text-red-300">
                        Anda memiliki akses penuh ke semua fitur dan data dalam sistem.
                    </p>
                </div>
                @elseif(\App\Helpers\PermissionHelper::hasRole('admin'))
                <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                    <h4 class="text-lg font-medium text-orange-800 dark:text-orange-200 mb-2">
                        🔧 Admin Access
                    </h4>
                    <p class="text-sm text-orange-700 dark:text-orange-300">
                        Anda dapat mengelola master data, user, dan referensi tarif.
                    </p>
                </div>
                @elseif(\App\Helpers\PermissionHelper::hasAnyRole(['bendahara-pengeluaran', 'bendahara-pengeluaran-pembantu']))
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h4 class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-2">
                        💰 Bendahara Access
                    </h4>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        @if(\App\Helpers\PermissionHelper::hasRole('bendahara-pengeluaran'))
                            Anda dapat mengelola semua dokumen tanpa batasan bidang.
                        @else
                            Anda dapat mengelola dokumen sesuai dengan bidang Anda.
                        @endif
                    </p>
                </div>
                @elseif(\App\Helpers\PermissionHelper::hasRole('sekretariat'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <h4 class="text-lg font-medium text-green-800 dark:text-green-200 mb-2">
                        📊 Sekretariat Access
                    </h4>
                    <p class="text-sm text-green-700 dark:text-green-300">
                        Anda hanya dapat mengakses fitur rekapitulasi sesuai dengan bidang Anda.
                    </p>
                </div>
                @endif

                {{-- Permission Debug Information (for development) --}}
                @if(config('app.debug'))
                <div class="mt-6 bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        🔍 Debug Information
                    </h4>
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        <p><strong>User ID:</strong> {{ auth()->id() }}</p>
                        <p><strong>Roles:</strong> {{ auth()->user()->getRoleNames()->implode(', ') }}</p>
                        <p><strong>Permissions:</strong> {{ auth()->user()->getAllPermissions()->pluck('name')->implode(', ') }}</p>
                        <p><strong>Can Access All Data:</strong> {{ \App\Helpers\PermissionHelper::canAccessAllData() ? 'Yes' : 'No' }}</p>
                        <p><strong>User Unit ID:</strong> {{ \App\Helpers\PermissionHelper::getUserUnitId() ?? 'No restriction' }}</p>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

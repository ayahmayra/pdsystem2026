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
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Kelola Permissions</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola permissions untuk: <strong>{{ $user->fullNameWithTitles() }}</strong>
                </p>
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

    <!-- User Info -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi User</h3>
                <div class="space-y-2">
                    <p><strong>Nama:</strong> {{ $user->fullNameWithTitles() }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Unit:</strong> {{ $user->unit?->fullName() ?? 'Tidak ada' }}</p>
                    <p><strong>Jabatan:</strong> {{ $user->position?->fullName() ?? 'Tidak ada' }}</p>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Roles Saat Ini</h3>
                <div class="space-y-2">
                    @if(count($userRoles) > 0)
                        @foreach($userRoles as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ ucfirst(str_replace('-', ' ', $role)) }}
                            </span>
                        @endforeach
                    @else
                        <p class="text-gray-500 dark:text-gray-400">Tidak ada role</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Management -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Permissions</h3>
                <div class="flex space-x-2">
                    <button wire:click="selectAllPermissions" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white dark:border-gray-500 dark:hover:bg-gray-700">
                        Pilih Semua
                    </button>
                    <button wire:click="resetToRolePermissions" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white dark:border-gray-500 dark:hover:bg-gray-700">
                        Reset ke Role
                    </button>
                    <button wire:click="clearAllPermissions" 
                            class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-600 dark:text-white dark:border-red-500 dark:hover:bg-red-700">
                        Hapus Semua
                    </button>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form wire:submit="savePermissions" class="space-y-6">
                @foreach($permissionGroups as $groupName => $group)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $group['name'] }}
                                </h4>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ count(array_filter($group['permissions'], fn($p) => in_array($p->name, $selectedPermissions))) }} / {{ count($group['permissions']) }} dipilih
                                    </span>
                                    <button type="button" 
                                            wire:click="toggleGroupPermissions('{{ $groupName }}')"
                                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        Toggle All
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($group['permissions'] as $permission)
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" 
                                               wire:model="selectedPermissions" 
                                               value="{{ $permission->name }}" 
                                               id="permission_{{ $permission->id }}"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                                        <label for="permission_{{ $permission->id }}" 
                                               class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $this->getPermissionDisplayName($permission->name) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('users.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white dark:border-gray-500 dark:hover:bg-gray-700">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Current Permissions Summary -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Ringkasan Permissions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Permissions dari Role</h4>
                <div class="space-y-1">
                    @php
                        $rolePermissions = [];
                        foreach($user->roles as $role) {
                            $rolePermissions = array_merge($rolePermissions, $role->permissions->pluck('name')->toArray());
                        }
                        $rolePermissions = array_unique($rolePermissions);
                    @endphp
                    @if(count($rolePermissions) > 0)
                        @foreach($rolePermissions as $permission)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ $this->getPermissionDisplayName($permission) }}
                            </span>
                        @endforeach
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada permissions dari role</p>
                    @endif
                </div>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Direct Permissions (Saat Ini)</h4>
                <div class="space-y-1">
                    @if(count($userPermissions) > 0)
                        @foreach($userPermissions as $permission)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                {{ $this->getPermissionDisplayName($permission) }}
                            </span>
                        @endforeach
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada direct permissions</p>
                    @endif
                </div>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Permissions yang Dipilih</h4>
                <div class="space-y-1">
                    @if(count($selectedPermissions) > 0)
                        @foreach($selectedPermissions as $permission)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $this->getPermissionDisplayName($permission) }}
                            </span>
                        @endforeach
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada permissions yang dipilih</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleGroupPermissions(groupName) {
        // This will be handled by Livewire
        @this.call('toggleGroupPermissions', groupName);
    }
</script>
@endpush

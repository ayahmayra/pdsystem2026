<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('roles.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Role
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Kelola Permissions Role</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola permissions untuk role: <strong>{{ ucfirst(str_replace('-', ' ', $role->name)) }}</strong>
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

    <!-- Role Info -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Role</h3>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Role:</span>
                        <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah User:</span>
                        <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ $role->users->count() }} user</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Permissions:</span>
                        <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ count($selectedPermissions) }} permissions</span>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <button wire:click="resetToDefault" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Reset ke Default
                    </button>
                    <button wire:click="clearAllPermissions" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Hapus Semua Permissions
                    </button>
                    <button wire:click="selectAllPermissions" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Pilih Semua Permissions
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Permissions</h3>
        
        <form wire:submit.prevent="savePermissions">
            <div class="space-y-6">
                @foreach($this->getPermissionGroups() as $groupName => $permissions)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white">
                                {{ $this->getGroupDisplayName($groupName) }}
                            </h4>
                            <button type="button" 
                                    wire:click="toggleGroupPermissions('{{ $groupName }}')"
                                    class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded">
                                Toggle All
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($permissions as $permission)
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" 
                                           wire:model="selectedPermissions" 
                                           value="{{ $permission->name }}"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $this->getPermissionDisplayName($permission->name) }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('roles.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Simpan Permissions
                </button>
            </div>
        </form>
    </div>

    <!-- Current Permissions Summary -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Ringkasan Permissions</h3>
        <div class="space-y-1">
            @if(count($selectedPermissions) > 0)
                @foreach($selectedPermissions as $permission)
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-2 mb-2">
                        {{ $this->getPermissionDisplayName($permission) }}
                    </span>
                @endforeach
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada permissions yang dipilih</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleGroupPermissions(groupName) {
        // This will be handled by Livewire
    }
</script>
@endpush

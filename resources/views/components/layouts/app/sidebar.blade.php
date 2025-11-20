<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        @fluxAppearance
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.header>
                <flux:sidebar.brand
                    href="{{ route('dashboard') }}"
                    name="{{ \App\Models\OrgSettings::getInstance()->short_name ?: 'PdSystem' }}"
                    wire:navigate
                >
                    <x-dynamic-app-logo />
                </flux:sidebar.brand>

                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

           

            <flux:sidebar.nav>
                @if(\App\Helpers\PermissionHelper::can('menu.dashboard'))
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate tooltip="{{ __('Dashboard') }}">{{ __('Dashboard') }}</flux:sidebar.item>
                @endif

                @if(\App\Helpers\PermissionHelper::can('menu.documents'))
                <flux:sidebar.item icon="document-text" :href="route('documents')" :current="request()->routeIs('documents')" wire:navigate  tooltip="{{ __('Dokumen') }}">{{ __('Dokumen') }}</flux:sidebar.item>
                @endif

                @if(\App\Helpers\PermissionHelper::can('menu.master-data'))
                <flux:sidebar.item icon="users" :href="route('master-data.index')" :current="request()->routeIs('master-data.*')" wire:navigate  tooltip="{{ __('Master Data') }}">{{ __('Master Data') }}</flux:sidebar.item>
                @endif

                @if(\App\Helpers\PermissionHelper::can('menu.location-routes'))
                <flux:sidebar.item icon="map" :href="route('location-routes.index')" :current="request()->routeIs('location-routes.*')" wire:navigate  tooltip="{{ __('Ref Lokasi & Rute') }}">{{ __('Ref Lokasi & Rute') }}</flux:sidebar.item>
                @endif

                @if(\App\Helpers\PermissionHelper::can('menu.reference-rates'))
                <flux:sidebar.item icon="calculator" :href="route('reference-rates.index')" :current="request()->routeIs('reference-rates.*')" wire:navigate  tooltip="{{ __('Referensi Tarif') }}">{{ __('Referensi Tarif') }}</flux:sidebar.item>
                @endif
            </flux:sidebar.nav>

            @if(\App\Helpers\PermissionHelper::can('menu.rekap'))
            <flux:sidebar.group expandable heading="{{ __('Rekapitulasi') }}" icon="chart-bar">
                <flux:sidebar.item icon="chart-bar" :href="route('rekap.global')" :current="request()->routeIs('rekap.global')" wire:navigate  tooltip="{{ __('Rekap Global') }}">{{ __('Rekap Global') }}</flux:sidebar.item>
                <flux:sidebar.item icon="users" :href="route('rekap.pegawai')" :current="request()->routeIs('rekap.pegawai')" wire:navigate  tooltip="{{ __('Rekap Pegawai') }}">{{ __('Rekap Pegawai') }}</flux:sidebar.item>
            </flux:sidebar.group>
            @endif

            {{-- <flux:spacer /> --}}

            @if(\App\Helpers\PermissionHelper::can('menu.configuration'))
            <flux:sidebar.group expandable heading="{{ __('Configuration') }}" icon="cog-6-tooth">
                @if(\App\Helpers\PermissionHelper::can('menu.organization'))
                <flux:sidebar.item :href="route('organization.show')" icon="building-office-2" wire:navigate  tooltip="{{ __('Organisasi') }}">{{ __('Organisasi') }}</flux:sidebar.item>
                @endif
                @if(\App\Helpers\PermissionHelper::can('menu.ranks'))
                <flux:sidebar.item icon="shield-check" :href="route('ranks.index')" :current="request()->routeIs('ranks.*')" wire:navigate  tooltip="{{ __('Data Pangkat') }}">{{ __('Data Pangkat') }}</flux:sidebar.item>
                @endif
                @if(\App\Helpers\PermissionHelper::can('menu.doc-number-formats'))
                <flux:sidebar.item icon="hashtag" :href="route('doc-number-formats.index')" :current="request()->routeIs('doc-number-formats.*')" wire:navigate  tooltip="{{ __('Format Penomoran') }}">{{ __('Format Penomoran') }}</flux:sidebar.item>
                @endif
                @if(\App\Helpers\PermissionHelper::can('menu.number-sequences'))
                <flux:sidebar.item icon="hashtag" :href="route('number-sequences.index')" :current="request()->routeIs('number-sequences.*')" wire:navigate  tooltip="{{ __('Number Sequence') }}">{{ __('Number Sequence') }}</flux:sidebar.item>
                @endif
                @if(\App\Helpers\PermissionHelper::can('menu.document-numbers'))
                <flux:sidebar.item icon="document-text" :href="route('document-numbers.index')" :current="request()->routeIs('document-numbers.*')" wire:navigate  tooltip="{{ __('Riwayat Nomor') }}">{{ __('Riwayat Nomor') }}</flux:sidebar.item>
                @endif
            </flux:sidebar.group>
            @endif
            <flux:sidebar.item icon="document-text" :href="route('documentation')" :current="request()->routeIs('documentation')" wire:navigate  tooltip="{{ __('Dokumentasi') }}">{{ __('Dokumentasi') }}</flux:sidebar.item>
            <flux:sidebar.spacer />

            {{-- <flux:sidebar.nav>
                <flux:sidebar.item icon="cog-6-tooth" :href="route('profile.show')" wire:navigate tooltip="{{ __('Settings') }}">{{ __('Settings') }}</flux:sidebar.item>
            </flux:sidebar.nav> --}}

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.show')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                        {{-- <flux:menu.item :href="route('organization.show')" icon="building-office-2" wire:navigate>{{ __('Organisasi') }}</flux:menu.item> --}}
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile Header -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.show')" icon="cog" wire:navigate >{{ __('Settings') }}</flux:menu.item>
                        <flux:menu.item :href="route('organization.show')" icon="building-office-2" wire:navigate >{{ __('Organisasi') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <script>
            document.addEventListener('alpine:init', () => {
                try {
                    Alpine.data('searchableSelect', (config) => ({
                    options: config.options || [],
                    selectedValue: config.selectedValue || null,
                    placeholder: config.placeholder || 'Pilih opsi...',
                    searchTerm: '',
                    open: false,
                    selectedIndex: 0,

                    init() {
                        // Set initial search term if there's a selected value
                        if (this.selectedValue) {
                            const selectedOption = this.options.find(option => option.id == this.selectedValue);
                            if (selectedOption) {
                                this.searchTerm = selectedOption.text;
                            }
                        }

                        this.$watch('selectedValue', (value) => {
                            if (value) {
                                const selectedOption = this.options.find(option => option.id == value);
                                if (selectedOption) {
                                    this.searchTerm = selectedOption.text;
                                }
                            } else {
                                this.searchTerm = '';
                            }
                        });

                        this.$watch('searchTerm', () => {
                            this.selectedIndex = 0;
                        });
                    },

                    get filteredOptions() {
                        if (!this.searchTerm) {
                            return this.options;
                        }
                        
                        const term = this.searchTerm.toLowerCase();
                        return this.options.filter(option => 
                            option.text.toLowerCase().includes(term) ||
                            option.name.toLowerCase().includes(term) ||
                            (option.nip && option.nip.toLowerCase().includes(term))
                        );
                    },

                    selectOption(option) {
                        this.selectedValue = option.id;
                        this.searchTerm = option.text;
                        this.open = false;
                    },

                    selectNext() {
                        if (this.selectedIndex < this.filteredOptions.length - 1) {
                            this.selectedIndex++;
                        }
                    },

                    selectPrevious() {
                        if (this.selectedIndex > 0) {
                            this.selectedIndex--;
                        }
                    },

                    selectCurrent() {
                        if (this.filteredOptions.length > 0) {
                            this.selectOption(this.filteredOptions[this.selectedIndex]);
                        }
                    }
                }));
                } catch (error) {
                    console.warn('Alpine.js searchableSelect initialization failed:', error);
                }
            });
        </script>

        @livewireScripts
        @fluxScripts
    </body>
</html>
